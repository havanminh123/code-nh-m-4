from flask import Flask, request, jsonify
import cv2
import numpy as np
import base64
import dlib
import sqlite3
import datetime

app = Flask(__name__)

# Bộ nhận diện khuôn mặt
face_detector = dlib.get_frontal_face_detector()
face_recognizer = dlib.face_recognition_model_v1("dlib_face_recognition_resnet_model_v1.dat")
shape_predictor = dlib.shape_predictor("shape_predictor_68_face_landmarks.dat")

# Kết nối cơ sở dữ liệu
def connect_db():
    conn = sqlite3.connect("employees.db")
    return conn

# Hàm giải mã ảnh base64
def decode_image(image_data):
    img = base64.b64decode(image_data.split(",")[1])
    np_arr = np.frombuffer(img, dtype=np.uint8)
    return cv2.imdecode(np_arr, cv2.IMREAD_COLOR)

# Hàm nhận diện khuôn mặt
def recognize_face(image):
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    faces = face_detector(gray)
    
    if len(faces) == 0:
        return None

    shape = shape_predictor(gray, faces[0])
    descriptor = face_recognizer.compute_face_descriptor(image, shape)
    return np.array(descriptor)

# Kiểm tra khuôn mặt nhân viên
def verify_employee(employee_id, face_descriptor):
    conn = connect_db()
    cursor = conn.cursor()
    
    cursor.execute("SELECT face_encoding FROM employees WHERE employee_id=?", (employee_id,))
    result = cursor.fetchone()

    conn.close()
    
    if result:
        stored_descriptor = np.array(eval(result[0]))
        distance = np.linalg.norm(stored_descriptor - face_descriptor)
        return distance < 0.6  # Ngưỡng xác thực
    return False

@app.route("/attendance", methods=["POST"])
def attendance():
    data = request.get_json()
    employee_id = data["employee_id"]
    image_data = data["image"]

    image = decode_image(image_data)
    face_descriptor = recognize_face(image)

    if face_descriptor is None:
        return jsonify({"message": "Không phát hiện khuôn mặt!"})

    if verify_employee(employee_id, face_descriptor):
        conn = connect_db()
        cursor = conn.cursor()

        timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        cursor.execute("INSERT INTO attendance (employee_id, time) VALUES (?, ?)", (employee_id, timestamp))
        conn.commit()
        conn.close()

        return jsonify({"message": f"Chấm công thành công cho nhân viên {employee_id} vào {timestamp}"})
    else:
        return jsonify({"message": "Khuôn mặt không khớp!"})

if __name__ == "__main__":
    app.run(debug=True)
