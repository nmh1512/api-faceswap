import os
import sys
import cv2
import insightface
# from firebase_utils import initialize_firebase, upload_to_firebase, check_existing_file
current_file = os.path.abspath(__file__)
storage_path = os.path.abspath(os.path.join(current_file, '..', '..', 'public', 'storage'))
def draw_on_fixed(self, img, faces):
    dimg = img.copy()
    for i in range(len(faces)):
        face = faces[i]
        box = face.bbox.astype(int) 
        color = (0, 0, 255)  
        cv2.rectangle(dimg, (box[0], box[1]), (box[2], box[3]), color, 2)
    return dimg

def process_faceswap(fb_id, theme_id, image_id, result_filename):
    current_dir = os.path.dirname(os.path.realpath(__file__))

    src_path    = os.path.join(storage_path, 'uploads', f"fb_image_{fb_id}.png")
    target_path = os.path.join(storage_path, 'models', 'images', theme_id, f"{image_id}.png")

    insightface.app.FaceAnalysis.draw_on = draw_on_fixed
    providers = ["CPUExecutionProvider"]

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

    src_faces = FACE_ANALYSER.get(src_frame)[0]

    target_faces = FACE_ANALYSER.get(target_frame)

    model_path = current_dir + '/models/inswapper_128.onnx'
    model_swap_insightface = insightface.model_zoo.get_model(model_path, providers=providers)

    img_fake = model_swap_insightface.get(img=target_frame, target_face=target_faces[0], source_face=src_faces, paste_back=True)

    result_dir = 'storage/results'
    if not os.path.exists(result_dir):
        os.makedirs(result_dir)

    result_path = os.path.join(result_dir, result_filename)

    result = cv2.imwrite(result_path, img_fake)
    # url = upload_to_firebase(result_path, result_filename)

    if (result):
        print('Swapface successfully')
    else:
        print('Swapface failure')


if __name__ == "__main__":
    # initialize_firebase()
    fb_id       = sys.argv[1]
    theme_id    = sys.argv[2]
    image_id    = sys.argv[3]

    result_filename = f'{fb_id}_{theme_id}_{image_id}.jpg'
    
    # if check_existing_file(result_filename):
    #     sys.exit(1)

    result_url = process_faceswap(fb_id, theme_id, image_id, result_filename)
    if result_url:
        print(f"Result URL: {result_url}")
