import React, { useEffect, useState } from 'react';
import styles from './VerifyPopup.module.css';

function VerifyPopup({ token, onClose }) {
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(true);
  const [success, setSuccess] = useState(false); 

  useEffect(() => {
    const verifyAccount = async () => {
      try {
        const res = await fetch(
          'http://localhost/stocument/backend/verify_account.php',
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({ token }),
          }
        );

        if (!res.ok) {
          throw new Error(`HTTP lỗi: ${res.status}`);
        }

        const data = await res.json();
        if (data.success) {
          setSuccess(true);
          setMessage(data.message || 'Xác minh thành công!');
        } else {
          setSuccess(false);
          setMessage(data.message || 'Xác minh thất bại.');
        }
      } catch (err) {
        setSuccess(false);
        setMessage(`Không thể kết nối đến server. Lỗi: ${err.message}`);
      } finally {
        setLoading(false);
      }
    };

    verifyAccount();
  }, [token]);

  return (
    <div className={styles.popupOverlay}>
      <div className={styles.popupContent}>
        <h2 className={styles.title}>Xác minh tài khoản</h2>
        <p className={styles.message}>
          {loading ? '🔄 Đang xác minh...' : message}
        </p>
        {!loading && (
          <button className={styles.closeButton} onClick={onClose}>
            Đóng
          </button>
        )}
      </div>
    </div>
  );
}

export default VerifyPopup;
