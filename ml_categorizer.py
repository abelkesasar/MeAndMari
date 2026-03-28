import os
import face_recognition
import mysql.connector
from PIL import Image

# Konfigurasi Database
db_config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'me_and_mari'
}

# Direktori Uploads
UPLOADS_DIR = 'uploads'

# Inisialisasi Database
conn = mysql.connector.connect(**db_config)
cursor = conn.cursor(dictionary=True)

def load_reference_encodings():
    """Memuat encoding wajah dari foto profil Abel dan Mari."""
    references = {}
    
    # Cari foto profil abel dan mari di direktori uploads
    files = os.listdir(UPLOADS_DIR)
    abel_img = next((f for f in files if 'profile_abel' in f), None)
    mari_img = next((f for f in files if 'profile_mari' in f), None)
    
    if abel_img:
        print(f"Loading reference for Abel: {abel_img}")
        img = face_recognition.load_image_file(os.path.join(UPLOADS_DIR, abel_img))
        encodings = face_recognition.face_encodings(img)
        if encodings:
            references['abel'] = encodings[0]
            
    if mari_img:
        print(f"Loading reference for Mari: {mari_img}")
        img = face_recognition.load_image_file(os.path.join(UPLOADS_DIR, mari_img))
        encodings = face_recognition.face_encodings(img)
        if encodings:
            references['mari'] = encodings[0]
            
    return references

def process_memories():
    """Memproses semua foto di tabel memories dan mendeteksi siapa yang ada di foto."""
    references = load_reference_encodings()
    if not references:
        print("Error: Foto profil Abel atau Mari tidak ditemukan sebagai referensi.")
        return

    cursor.execute("SELECT id, photo FROM memories")
    memories = cursor.fetchall()
    
    for memory in memories:
        file_path = os.path.join(UPLOADS_DIR, memory['photo'])
        if not os.path.exists(file_path):
            continue
            
        print(f"Processing image: {memory['photo']}")
        
        try:
            image = face_recognition.load_image_file(file_path)
            face_encodings = face_recognition.face_encodings(image)
            
            detected = []
            
            for face_encoding in face_encodings:
                # Cek Abel
                if 'abel' in references:
                    matches = face_recognition.compare_faces([references['abel']], face_encoding, tolerance=0.6)
                    if matches[0]:
                        detected.append('abel')
                        continue
                
                # Cek Mari
                if 'mari' in references:
                    matches = face_recognition.compare_faces([references['mari']], face_encoding, tolerance=0.6)
                    if matches[0]:
                        detected.append('mari')
                        continue
            
            # Kategorisasi berdasarkan hasil deteksi
            unique_detected = list(set(detected))
            
            result = 'unknown'
            if len(unique_detected) > 1:
                result = 'both'
            elif len(unique_detected) == 1:
                result = unique_detected[0]
            
            print(f"Result for {memory['photo']}: {result}")
            
            # Update Database
            update_query = "UPDATE memories SET detected_person = %s WHERE id = %s"
            cursor.execute(update_query, (result, memory['id']))
            conn.commit()
            
        except Exception as e:
            print(f"Error processing {memory['photo']}: {e}")

if __name__ == "__main__":
    print("Starting ML Face Detection...")
    process_memories()
    print("Done!")
    cursor.close()
    conn.close()
