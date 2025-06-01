import React, { useState } from 'react';
import styles from './ForgotPass.module.css';
import { useTheme } from '../../contexts/ThemeContext';

function ForgotPassword({ setOpenForgot }) {
  const { theme } = useTheme();
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false); // Thêm state loading

  const getStyle = (base, dark) =>
    theme === 'dark' ? `${base} ${dark}` : base;

  const validateEmail = (email) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setMessage('');
    setLoading(true); // Bắt đầu loading

    if (!validateEmail(email)) {
      setError('Vui lòng nhập email hợp lệ.');
      setLoading(false);
      return;
    }

    try {
      const res = await fetch('http://localhost/stocument/backend/forgot_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ email }),
      });

      const data = await res.json();

      if (data.success) {
        setMessage(data.message);
        setSubmitted(true);
      } else {
        setError(data.message || 'Đã xảy ra lỗi.');
      }
    } catch (err) {
      setError('Không thể gửi yêu cầu. Vui lòng thử lại.');
      console.error(err);
    } finally {
      setLoading(false); // Kết thúc loading
    }
  };

  return (
    <div className={getStyle(styles.layout, styles.darkLayout)}>
      <div className={getStyle(styles.forgotPasswordWrapper, styles.darkForgotPasswordWrapper)}>
        <h1 className={getStyle(styles.forgotPasswordTitle, styles.darkForgotPasswordTitle)}>
          Quên mật khẩu
        </h1>

        {!submitted ? (
          <form
            onSubmit={handleSubmit}
            className={getStyle(styles.forgotPasswordForm, styles.darkForgotPasswordForm)}
          >
            <input
              type="email"
              placeholder="Nhập email của bạn"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className={getStyle(styles.inputField, styles.darkInputField)}
              disabled={loading} // disable input khi loading
            />

            {error && (
              <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>{error}</p>
            )}

            {loading ? (
              <p className={getStyle(styles.loadingMessage, styles.darkLoadingMessage)}>
                🔄 Đang thực hiện...
              </p>
            ) : (
              <button
                type="submit"
                className={getStyle(styles.submitBtn, styles.darkSubmitBtn)}
              >
                Gửi liên kết khôi phục
              </button>
            )}
          </form>
        ) : (
          <div>
            <p className={getStyle(styles.successMessage, styles.darkSuccessMessage)}>
              {message}
            </p>
            <button
              className={getStyle(styles.submitBtn, styles.darkSubmitBtn)}
              onClick={() => setOpenForgot(false)}
            >
              Đóng
            </button>
          </div>
        )}
      </div>
    </div>
  );
}

export default ForgotPassword;
