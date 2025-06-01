
document.addEventListener("DOMContentLoaded", () => {
    load_general();
    load_contacts();
    get_members();

    // Chỉnh sửa thông tin chung
    document.getElementById('general_s_form').addEventListener('submit', function (e) {
    e.preventDefault();
    const data = new FormData(this);
    data.append('upd_general', 1);

    fetch('ajax/information.php', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            alert('success', 'Cập nhật thành công!');
            load_general();

            var modal = bootstrap.Modal.getInstance(document.getElementById('general-s'));
            modal.hide();
        } else {
            alert('error', res.message || 'Cập nhật thất bại!');
        }
    })
    .catch(err => {
        alert('error', 'Có lỗi xảy ra trong quá trình gửi dữ liệu!');
        console.error(err);
    });
});

    // Chỉnh sửa thông tin liên hệ
    document.getElementById('contacts_s_form').addEventListener('submit', function (e) {
    e.preventDefault();
    const data = new FormData(this);
    data.append('upd_contacts', 1);  // BẮT BUỘC phải có để PHP xử lý update
    fetch('ajax/information.php', {
        method: 'POST',
        body: data
    }).then(res => res.json())
    .then(res => {
        if (res.success) {
            alert('Cập nhật liên hệ thành công!');
            load_contacts();
            var modal = bootstrap.Modal.getInstance(document.getElementById('contacts-s'));
            modal.hide();
        } else {
            alert('Lỗi: ' + (res.msg || 'Không rõ'));
        }
    }).catch(err => {
        alert('Lỗi kết nối server!');
        console.error(err);
    });
});
//them thanh vien
 document.getElementById('team_s_form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = document.getElementById('team_s_form');
    const data = new FormData(form);
    data.append('add_member', 1);

    fetch('ajax/information.php', {
        method: 'POST',
        body: data
    }).then(res => res.json())
    .then(res => {
        if (res.success) {
            alert('Thêm thành viên thành công!');
            form.reset();
            var modal = bootstrap.Modal.getInstance(document.getElementById('team-s'));
            modal.hide();
            get_members();
        } else {
            alert('Lỗi: ' + (res.msg || 'Không rõ'));
        }
    }).catch(err => {
        alert('Lỗi kết nối server!');
        console.error(err);
    });
});

});

// Load thông tin chung
function load_general() {
    fetch('ajax/information.php?get_general').then(res => res.json()).then(data => {
        document.getElementById('site_title').textContent = data.site_title;
        document.getElementById('site_about').textContent = data.site_about;
        document.getElementById('site_title_inp').value = data.site_title;
        document.getElementById('site_about_inp').value = data.site_about;
    });
}

// Load thông tin liên hệ
function load_contacts() {
    fetch('ajax/information.php?get_contacts').then(res => res.json()).then(data => {
        document.getElementById('address').textContent = data.address;
        document.getElementById('gmap').textContent = data.gmap;
        document.getElementById('pn1').textContent = data.pn1;
        document.getElementById('pn2').textContent = data.pn2;
        document.getElementById('email').textContent = data.email;
        document.getElementById('fb').textContent = data.fb;
        document.getElementById('insta').textContent = data.insta;
        document.getElementById('tw').textContent = data.tw;
        document.getElementById('iframe').src = data.iframe;

        // Gán dữ liệu lại cho modal
        document.getElementById('address_inp').value = data.address;
        document.getElementById('gmap_inp').value = data.gmap;
        document.getElementById('pn1_inp').value = data.pn1;
        document.getElementById('pn2_inp').value = data.pn2;
        document.getElementById('email_inp').value = data.email;
        document.getElementById('fb_inp').value = data.fb;
        document.getElementById('insta_inp').value = data.insta;
        document.getElementById('tw_inp').value = data.tw;
        document.getElementById('iframe_inp').value = data.iframe;
    });
}

// Load danh sách đội ngũ quản lý
function get_members(){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/information.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function(){
        document.getElementById('team-data').innerHTML = this.responseText;
    }

    xhr.send('get_members=1');  // bắt buộc gửi giá trị
}
// Xoa danh sách đội ngũ quản lý
function rem_member(sr_no){
    if (!confirm('Bạn có chắc muốn xóa thành viên này không?')) return;

    const formData = new FormData();
    formData.append("rem_member", 1);
    formData.append("sr_no", sr_no);

    fetch('ajax/information.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        console.log('Response xóa thành viên:', data);
        if (data.trim() === 'success') {
            alert("Xóa thành viên thành công!");
            get_members();
        } else {
            alert("Lỗi khi xóa thành viên.");
        }
    })
    .catch(err => {
        alert('Lỗi kết nối server!');
        console.error(err);
    });
}
