const imageUpload = document.getElementById("imgUpload");
const imagePreview = document.getElementById("imgPreview");

imageUpload.onchange = evt => {
    const [file] = imageUpload.files;
    if (file) {
        imagePreview.src = URL.createObjectURL(file);
    }
}