try:
    import argparse
    import numpy as np
    from PIL import Image

    ap = argparse.ArgumentParser()
    ap.add_argument("-i", "--img", type=str, required=True, help="Image path")
    args = vars(ap.parse_args())

    img_path = args['img']
    img = Image.open(img_path, 'r')
    ar_img = np.asarray(img)
    img.close()
    ar_img = np.array(ar_img)
    # ar_img[ar_img >= 150] = 0
    ar_img = 255 - ar_img
    # Image.fromarray(ar_img).show()
    num = np.sum(ar_img)
    print(f"number:{num}")
    exit(0)
except Exception as ex:
    print('error')
    exit(1)
