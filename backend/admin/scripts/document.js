function get_documents() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/document.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        document.getElementById('document-data').innerHTML = this.responseText;
    };
    xhr.send('get_documents=1');
}


function search_document(title) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/document.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        document.getElementById('document-data').innerHTML = this.responseText;
    }

    xhr.send('search_document=1&title=' + encodeURIComponent(title));
}

function remove_document(doc_id) {
     
    if (!confirm("Bạn có chắc muốn xoá tài liệu này không?")) return;
    
    let data = new FormData();
    data.append('doc_id', doc_id);
    data.append('delete_document', '1');
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/document.php", true);
    
    xhr.onload = function() {
        console.log("Response:", this.responseText);
        
        if (this.responseText == 1) {
            alert('success', 'Đã xóa tài liệu thành công');    
            let row = document.getElementById('doc-row-' + doc_id);
            if (row) {
                row.remove();
            } else {
                console.log("Cần tải lại trang để cập nhật giao diện");              
                get_documents(); 
            }
        } else if (this.responseText == 2) {
            alert('warning', 'Xóa tài liệu trong database thành công nhưng không thể xóa file trên server');
        } else {
            alert('error', 'Xoá tài liệu thất bại!');
        }
    };
    xhr.onerror = function() {
        alert('error', 'Lỗi kết nối mạng. Vui lòng kiểm tra lại kết nối.');
    };
    xhr.send(data);
}
window.onload = function () {
    get_documents(); 
}
