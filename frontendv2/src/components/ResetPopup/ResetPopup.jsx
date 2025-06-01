import React, { useState } from 'react';
import styles from './ResetPopup.module.css';

function ResetPopup({ token, onClose }) {
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [showPassword, setShowPassword] = useState(false); // Thêm state để kiểm soát hiển thị mật khẩu

  const handleReset = async () => {
    setError('');
    if (!password || !confirm) {
      setError('Vui lòng điền đầy đủ các trường.');
      return;
    }
    if (password.length < 8) {
      setError('Mật khẩu phải có ít nhất 8 ký tự.');
      return;
    }
    if (password !== confirm) {
      setError('Mật khẩu nhập lại không khớp.');
      return;
    }
    try {
      const res = await fetch('http://localhost/stocument/backend/reset_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ token, password }),
      });

      const data = await res.json();
      if (data.success) {
        setSuccess('✅ Mật khẩu đã được đặt lại!');
        // Xóa resetToken khỏi URL
        const newUrl = window.location.pathname;
        window.history.replaceState(null, '', newUrl);

        // Đóng popup sau 2s
        setTimeout(onClose, 2000);
      }
    } catch (err) {
      console.error(err);
      setError('⚠️ Lỗi máy chủ.');
    }
  };

  return (
    <div className={styles.popupOverlay}>
      <div className={styles.popupContent}>
        <h2 className={styles.title}>Đổi mật khẩu</h2>
        {success ? (
          <>
            <p style={{ color: 'green' }}>{success}</p>
            <p className={styles.closeButton} onClick={onClose}>Đóng</p>
          </>
        ) : (
          <>
            <input
              className={styles.inputField}
              type={showPassword ? 'text' : 'password'}  // Điều chỉnh kiểu hiển thị mật khẩu
              placeholder="Mật khẩu mới"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
            <input
              className={styles.inputField}
              type={showPassword ? 'text' : 'password'}  // Điều chỉnh kiểu hiển thị mật khẩu
              placeholder="Nhập lại mật khẩu"
              value={confirm}
              onChange={(e) => setConfirm(e.target.value)}
            />
            <div className={styles.checkboxContainer}>
              <input
                type="checkbox"
                id="showPassword"
                checked={showPassword}
                onChange={() => setShowPassword(!showPassword)}
              />
              <label htmlFor="showPassword"> Hiển thị mật khẩu</label>
            </div>
            {error && <p className={styles.error}>{error}</p>}
            <button className={styles.button} onClick={handleReset}>Xác nhận</button>
            <p className={styles.closeButton} onClick={onClose}>Đóng</p>
          </>
        )}
      </div>
    </div>
  );
}

export default ResetPopup;
