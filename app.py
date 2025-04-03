import base64
import io
import numpy as np
from flask import Flask, request, jsonify
from PIL import Image
import face_recognition
from flask_cors import CORS
import mysql.connector
from datetime import datetime, time

app = Flask(__name__)
CORS(app)

# Kết nối MySQL
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="quanly_nhansu"
)

def get_known_faces():
    """Tải trước dữ liệu khuôn mặt từ CSDL"""
    cursor = db.cursor()
    cursor.execute("SELECT employee_id, file_name FROM images")
    all_images = cursor.fetchall()
    
    known_encodings = []
    employee_ids = []
    
    for emp_id, img_path in all_images:
        try:
            image = face_recognition.load_image_file(f"pages/{img_path}")
            encoding = face_recognition.face_encodings(image)
            if encoding:
                known_encodings.append(encoding[0])
                employee_ids.append(emp_id)
        except Exception as e:
            print(f"Lỗi tải ảnh {img_path}: {e}")
    
    return known_encodings, employee_ids

KNOWN_ENCODINGS, EMPLOYEE_IDS = get_known_faces()

@app.route('/check_face', methods=['POST'])
def check_face():
    try:
        data = request.json
        img_data = data['image'].split(",")[1]
        img_bytes = base64.b64decode(img_data)
        img = Image.open(io.BytesIO(img_bytes))
        img = np.array(img)

        face_locations = face_recognition.face_locations(img)
        if not face_locations:
            return jsonify({"message": "Không phát hiện khuôn mặt!"})

        face_encodings = face_recognition.face_encodings(img, face_locations)
        best_match_id = None
        best_distance = float("inf")
        threshold = 0.5

        for encoding in face_encodings:
            distances = face_recognition.face_distance(KNOWN_ENCODINGS, encoding)
            min_distance = min(distances)
            
            if min_distance < best_distance and min_distance < threshold:
                best_distance = min_distance
                best_match_id = EMPLOYEE_IDS[distances.tolist().index(min_distance)]
        
        if best_match_id:
            cursor = db.cursor()
            cursor.execute("SELECT id, ma_nv, ten_nv FROM nhanvien WHERE id = %s", (best_match_id,))
            employee_data = cursor.fetchone()
            
            if employee_data:
                employee_info = {
                    "id": employee_data[0],
                    "ma_nv": employee_data[1],
                    "ten_nv": employee_data[2],
                }
                return jsonify({
                    "message": "Khuôn mặt được nhận diện!",
                    "employee_info": employee_info
                })

        return jsonify({"message": "Không tìm thấy nhân viên phù hợp!"})
    
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/check_in', methods=['POST'])
def check_in():
    try:
        data = request.json
        img_data = data['image'].split(",")[1]
        img_bytes = base64.b64decode(img_data)
        img = Image.open(io.BytesIO(img_bytes))
        img = np.array(img)

        face_locations = face_recognition.face_locations(img)
        if not face_locations:
            return jsonify({"message": "Không phát hiện khuôn mặt!"})

        face_encodings = face_recognition.face_encodings(img, face_locations)
        best_match_id = None
        best_distance = float("inf")
        threshold = 0.5

        for encoding in face_encodings:
            distances = face_recognition.face_distance(KNOWN_ENCODINGS, encoding)
            min_distance = min(distances)
            
            if min_distance < best_distance and min_distance < threshold:
                best_distance = min_distance
                best_match_id = EMPLOYEE_IDS[distances.tolist().index(min_distance)]
        
        if best_match_id:
            cursor = db.cursor()
            current_time = datetime.now()

            # Lưu giờ chấm công
            check_in_time = current_time

            # Cập nhật giờ chấm công gần nhất
            cursor.execute("SELECT check_in FROM chan_cong WHERE employee_id = %s AND DATE(check_in) = CURDATE() ORDER BY check_in DESC LIMIT 1", (best_match_id,))
            check_in_record = cursor.fetchone()

            if check_in_record:
                # Cập nhật giờ chấm công
                cursor.execute("UPDATE chan_cong SET check_in = %s WHERE employee_id = %s AND DATE(check_in) = CURDATE()", (check_in_time, best_match_id))
                db.commit()
                message = "Cập nhật giờ chấm công vào thành công!"
            else:
                # Chấm công lần đầu trong ngày
                cursor.execute("INSERT INTO chan_cong (employee_id, check_in) VALUES (%s, %s)", (best_match_id, check_in_time))
                db.commit()
                message = "Chấm công vào thành công!"

            return jsonify({"message": message})

        return jsonify({"message": "Không tìm thấy nhân viên phù hợp!"})

    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/calculate_salary', methods=['POST'])
def calculate_salary():
    try:
        cursor = db.cursor()
        cursor.execute("SELECT employee_id, check_in, check_out FROM chan_cong WHERE DATE(check_in) = CURDATE()")
        records = cursor.fetchall()
        
        salary_data = []
        for record in records:
            employee_id, check_in, check_out = record
            check_in_time = check_in.time()
            check_out_time = check_out.time() if check_out else datetime.now().time()

            # Chỉ tính giờ công trong khoảng 8h - 17h
            if check_in_time < time(9, 0):
                effective_check_in = time(9, 0)  # Chỉ tính từ 8h
            else:
                effective_check_in = check_in_time

            if check_out_time > time(17, 0):
                effective_check_out = time(17, 0)  # Chỉ tính đến 17h
            else:
                effective_check_out = check_out_time

            # Tính tổng số giờ làm
            hours_worked = (datetime.combine(datetime.today(), effective_check_out) - datetime.combine(datetime.today(), effective_check_in)).total_seconds() / 3600

            salary_data.append({
                "employee_id": employee_id,
                "hours_worked": hours_worked
            })

        return jsonify(salary_data)

    except Exception as e:
        return jsonify({"error": str(e)}), 500
    
@app.route('/check_out', methods=['POST'])
def check_out():
    try:
        data = request.json
        img_data = data['image'].split(",")[1]
        img_bytes = base64.b64decode(img_data)
        img = Image.open(io.BytesIO(img_bytes))
        img = np.array(img)

        face_locations = face_recognition.face_locations(img)
        if not face_locations:
            return jsonify({"message": "Không phát hiện khuôn mặt!"})

        face_encodings = face_recognition.face_encodings(img, face_locations)
        best_match_id = None
        best_distance = float("inf")
        threshold = 0.5

        for encoding in face_encodings:
            distances = face_recognition.face_distance(KNOWN_ENCODINGS, encoding)
            min_distance = min(distances)
            
            if min_distance < best_distance and min_distance < threshold:
                best_distance = min_distance
                best_match_id = EMPLOYEE_IDS[distances.tolist().index(min_distance)]
        
        if best_match_id:
            cursor = db.cursor()
            current_time = datetime.now().time()

            # Kiểm tra giờ chấm công vào
            cursor.execute("SELECT check_in FROM chan_cong WHERE employee_id = %s AND DATE(check_in) = CURDATE()", (best_match_id,))
            check_in_record = cursor.fetchone()

            if check_in_record:
                check_in_time = check_in_record[0].time()

                if current_time < time(17, 0):
                    return jsonify({"message": "Bạn chỉ có thể chấm công ra sau 17h!"})

                # Chấm công ra
                cursor.execute("UPDATE chan_cong SET check_out = NOW() WHERE employee_id = %s AND DATE(check_in) = CURDATE()", (best_match_id,))
                db.commit()
                
                return jsonify({"message": "Chấm công ra thành công!"})

            return jsonify({"message": "Không tìm thấy giờ chấm công vào!"})

    except Exception as e:
        return jsonify({"error": str(e)}), 500
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)