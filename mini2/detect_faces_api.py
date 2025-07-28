# detect_faces_api.py

from flask import Flask, request, jsonify
from deepface import DeepFace
from datetime import datetime
import random 
import cv2
import os
import numpy as np
import json 

app = Flask(__name__)

db_path = "face_db/"
model_name = "ArcFace"
detector_backend = "retinaface"

# def detect_and_recognize(image_path):
#     detected_faces = DeepFace.extract_faces(
#         img_path=image_path,
#         detector_backend=detector_backend,
#         enforce_detection=False,
#         align=True
#     )

#     results = []
#     for i, face in enumerate(detected_faces):
#         face_img_rgb = face["face"]
#         temp_path = f"temp_face_{i}.jpg"
#         if face_img_rgb.dtype != np.uint8:
#             face_img_rgb = (face_img_rgb * 255).astype(np.uint8)

#         face_img_bgr = cv2.cvtColor(face_img_rgb, cv2.COLOR_RGB2BGR)
#         cv2.imwrite(temp_path, face_img_bgr)

#         df = DeepFace.find(
#             img_path=temp_path,
#             db_path=db_path,
#             model_name=model_name,
#             detector_backend=detector_backend,
#             enforce_detection=False
#         )[0]

#         if not df.empty:
#             top_match = df.iloc[0]
#             results.append({
#                 "face_index": i,
#                 "identity": os.path.basename(os.path.dirname(top_match["identity"])),
#                 "distance": top_match["distance"]
#             })
#         else:
#             results.append({
#                 "face_index": i,
#                 "identity": "Unknown",
#                 "distance": None
#             })

#         os.remove(temp_path)

#     return results

def detect_and_recognize(image_path):
    print(f"We are here")
    try:
        detected_faces = DeepFace.extract_faces(
            img_path=image_path,
            detector_backend=detector_backend,
            enforce_detection=False,
            align=True
        ) 
  
        # Create folder name for unrecognized faces
        folder_name = datetime.now().strftime("%Y%m%d_%H%M%S") + f"_unrecognized_{random.randint(1000, 9999)}"
        unrecognized_dir = os.path.join("unrecognized_faces", folder_name)
        os.makedirs(unrecognized_dir, exist_ok=True)
   
        results = []

        for i, face in enumerate(detected_faces):
            face_img_rgb = face["face"]
            temp_path = f"temp_face_{i}.jpg"

            if face_img_rgb.dtype != np.uint8:
                face_img_rgb = (face_img_rgb * 255).astype(np.uint8)

            face_img_bgr = cv2.cvtColor(face_img_rgb, cv2.COLOR_RGB2BGR)
            cv2.imwrite(temp_path, face_img_bgr)

            df = DeepFace.find(
                img_path=temp_path,
                db_path=db_path,
                model_name=model_name,
                detector_backend=detector_backend,
                enforce_detection=False
            )[0]

            if not df.empty:
                top_match = df.iloc[0]
                results.append({
                    "face_index": i,
                    "identity": os.path.basename(os.path.dirname(top_match["identity"])),
                    "distance": top_match["distance"]
                })
            else:
                # Save unknown face
                save_path = os.path.join(unrecognized_dir, f"face_{i}.jpg")
                cv2.imwrite(save_path, face_img_bgr)

                results.append({
                    "face_index": i,
                    "identity": "Unknown",
                    "distance": None,
                    "image_path": save_path
                })

            os.remove(temp_path)
        print(f" Final results : {results}")
    except Exception as e:
        print(str(e))
        return jsonify({"error": str(e)}), 500
        print(f"We are here 2")
    return results

@app.route('/detect_faces', methods=['POST'])
def detect_faces_api():
    print(f"PHP called Flask API at endpoint /your-api-endpoint, method: {request.method}")
    data = request.get_json()
    image_path = data.get('image_path')
    if not image_path:
        return jsonify({"error": "image_path missing"}), 400
    
    try:
        results = detect_and_recognize(image_path)
        return jsonify({"results": results})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
