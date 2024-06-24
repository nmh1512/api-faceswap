import os
import firebase_admin
from firebase_admin import credentials, storage
from datetime import datetime, timedelta


def initialize_firebase():
    current_dir = os.path.dirname(os.path.realpath(__file__))
    credentials_path = os.path.join(current_dir, 'credentials', 'filebase_credential.json')
    
    cred = credentials.Certificate(credentials_path)
    
    firebase_admin.initialize_app(cred, {
        'storageBucket': 'faceswap-cfcb8.appspot.com'
    })

def check_existing_file(result_filename):
    bucket = storage.bucket()
    blob = bucket.blob(f"results/{result_filename}")
    
    if blob.exists():
        url = blob.public_url
        print(f"File {result_filename} already exists on Firebase Storage.")
        print(f"Public URL of existing file: {url}")
        return url
    
    return None

def upload_to_firebase(result_image_path, result_filename):
    bucket = storage.bucket()
    blob = bucket.blob(f"results/{result_filename}")

    blob.upload_from_filename(result_image_path)

    blob.make_public()
    url = blob.public_url
    print(f"File {result_filename} uploaded to Firebase Storage.")
    print(f"Public URL of uploaded file: {url}")

    return url
