function remove_comment(com_id) {
 if (!confirm("Bạn có chắc muốn xoá bình luận này không?")) return;

    let data = new FormData();
    data.append('com_id', com_id);
    data.append('delete_comment', '1');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/comment.php", true);

    xhr.onload = function () {
        let res = xhr.responseText ? xhr.responseText.trim() : "null";

        console.log("Response raw:", xhr.responseText);
        console.log("Trimmed:", res);

        if (res === "1") {
            alert("Đã xoá bình luận thành công");
            let row = document.getElementById('comment-row-' + com_id);
            if (row) {
                row.remove();
            } else {
                get_comments();
            }
        } else {
            alert("Xoá bình luận thất bại!");
        }
    };

    xhr.onerror = function () {
        alert("Lỗi kết nối mạng. Vui lòng thử lại.");
    };

    xhr.send(data);
}

function get_comments() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/comment.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        document.getElementById('comment-data').innerHTML = this.responseText;
    };
    xhr.send('get_comments=1');
}

function search_comments(query) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/comment.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        document.getElementById('comment-data').innerHTML = this.responseText;
    };
    xhr.send('search_comments=1&query=' + encodeURIComponent(query));
}

window.onload = function () {
    get_comments();
}


