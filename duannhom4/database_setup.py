import sqlite3

conn = sqlite3.connect("employees.db")
cursor = conn.cursor()

# Tạo bảng nhân viên
cursor.execute("""
CREATE TABLE IF NOT EXISTS employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    face_encoding TEXT NOT NULL
)
""")

# Tạo bảng chấm công
cursor.execute("""
CREATE TABLE IF NOT EXISTS attendance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id TEXT NOT NULL,
    time TEXT NOT NULL
)
""")

conn.commit()
conn.close()

print("Database created successfully!")
