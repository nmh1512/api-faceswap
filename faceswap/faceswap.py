import os
import sys
import cv2
import insightface
import uuid

current_dir = os.path.dirname(os.path.realpath(__file__))

def draw_on_fixed(self, img, faces):
    dimg = img.copy()
    for i in range(len(faces)):
        face = faces[i]
        box = face.bbox.astype(int) 
        color = (0, 0, 255)  
        cv2.rectangle(dimg, (box[0], box[1]), (box[2], box[3]), color, 2)
    return dimg

insightface.app.FaceAnalysis.draw_on = draw_on_fixed

providers = ["CPUExecutionProvider"]

src_path = sys.argv[1]
target_path = sys.argv[2]

target_frame = cv2.imread(target_path)
if target_frame is None:
    print(f"Error: cannot read target image from {target_path}")
    sys.exit(1)

src_frame = cv2.imread(src_path)
if src_frame is None:
    print(f"Error: cannot read source image from {src_path}")
    sys.exit(1)

FACE_ANALYSER = insightface.app.FaceAnalysis(
                    name="buffalo_l",
                    root=current_dir, 
                    providers=providers,
                    allowed_modules=["landmark_3d_68", "landmark_2d_106", "detection", "recognition"]
                )
FACE_ANALYSER.prepare(
                ctx_id=0
            )

src_faces = FACE_ANALYSER.get(src_frame)[-1]

target_faces = FACE_ANALYSER.get(target_frame)

model_path = current_dir + '/models/inswapper_128.onnx'
model_swap_insightface = insightface.model_zoo.get_model(model_path, providers=providers)

img_fake = model_swap_insightface.get(img=target_frame, target_face=target_faces[0], source_face=src_faces, paste_back=True)

result_dir = 'storage/results'
if not os.path.exists(result_dir):
    os.makedirs(result_dir)

result_filename = f'result_{uuid.uuid4()}.jpg'
result_path = os.path.join(result_dir, result_filename)

cv2.imwrite(result_path, img_fake)
print(result_filename)