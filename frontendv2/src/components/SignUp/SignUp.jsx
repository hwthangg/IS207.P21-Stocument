import React, { useState } from 'react';
import { AiOutlineEye, AiOutlineEyeInvisible } from 'react-icons/ai';
import styles from './SignUp.module.css';
import { useTheme } from '../../contexts/ThemeContext';
import { IoCloseCircle } from 'react-icons/io5';

function RegisterForm(props) {
  const [username, setUsername] = useState('');
  const [fullName, setFullName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  const [usernameError, setUsernameError] = useState('');
  const [fullNameError, setFullNameError] = useState('');
  const [emailError, setEmailError] = useState('');
  const [passwordError, setPasswordError] = useState('');
  const [confirmPasswordError, setConfirmPasswordError] = useState('');
  const [serverError, setServerError] = useState('');

  // State để hiện mật khẩu
  const [isPasswordVisible, setIsPasswordVisible] = useState(false);
  const [isConfirmVisible, setIsConfirmVisible] = useState(false);

  // State để lưu trạng thái và message khi đăng ký thành công
  const [registrationSuccess, setRegistrationSuccess] = useState(false);
  const [registrationMessage, setRegistrationMessage] = useState('');

  // State loading
  const [loading, setLoading] = useState(false);

  const { theme } = useTheme();
  const getStyle = (baseStyle, darkStyle) =>
    theme === 'dark' ? `${baseStyle} ${darkStyle}` : baseStyle;

  const validateEmail = (input) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(input);
  };

  const handleRegister = async (e) => {
    e.preventDefault();
    // Reset lỗi
    setUsernameError('');
    setFullNameError('');
    setEmailError('');
    setPasswordError('');
    setConfirmPasswordError('');
    setServerError('');

    let isValid = true;

    // Validate từng trường
    if (!username.trim()) {
      setUsernameError('Vui lòng nhập tên đăng nhập.');
      isValid = false;
    }
    if (!fullName.trim()) {
      setFullNameError('Vui lòng nhập họ và tên.');
      isValid = false;
    }
    if (!validateEmail(email)) {
      setEmailError('Vui lòng nhập email hợp lệ.');
      isValid = false;
    }
    if (password.length < 8) {
      setPasswordError('Mật khẩu phải có ít nhất 8 ký tự.');
      isValid = false;
    }
    if (password !== confirmPassword) {
      setConfirmPasswordError('Mật khẩu không khớp.');
      isValid = false;
    }

    if (!isValid) return;

    // Nếu hợp lệ, gọi API
    setLoading(true);
    try {
      const res = await fetch(
        'http://localhost/stocument/backend/signup.php',
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({
            username,
            fullname: fullName,
            email,
            password,
            repassword: confirmPassword,
          }),
        }
      );

      const data = await res.json();

      if (data.success) {
        setRegistrationSuccess(true);
        setRegistrationMessage(data.message || 'Đăng ký thành công!');
      } else {
        // Hiện lỗi chung từ server
        setServerError(data.message || 'Đã xảy ra lỗi khi đăng ký.');
      }
    } catch (err) {
      console.error('Lỗi khi gửi yêu cầu:', err);
      setServerError('Lỗi máy chủ hoặc kết nối. Vui lòng thử lại.');
    } finally {
      setLoading(false);
    }
  };

  const togglePasswordVisibility = () =>
    setIsPasswordVisible((v) => !v);
  const toggleConfirmVisibility = () =>
    setIsConfirmVisible((v) => !v);

  // Nếu đăng ký thành công, chỉ hiển thị thông báo và nút Đóng
  if (registrationSuccess) {
    return (
      <div className={getStyle(styles.layout, styles.darkLayout)}>
        <div className={getStyle(styles.signupWrapper, styles.darkSignupWrapper)}>
          
          <h1 className={getStyle(styles.signupTitle, styles.darkSignupTitle)}>
            {registrationMessage}
          </h1>
          <button
            type="button"
            className={getStyle(styles.closeButton, styles.darkCloseButton)}
            onClick={() => props.setOpenSignUp(false)}
          >
            Đóng
          </button>
        </div>
      </div>
    );
  }

  // Giao diện form đăng ký
  return (
    <div className={getStyle(styles.layout, styles.darkLayout)}>
      <div className={getStyle(styles.signupWrapper, styles.darkSignupWrapper)} style={{position:'relative'}}>
        <div style={{position:'absolute', right: 0, top:0}} onClick={()=>props.setOpenSignUp(false)}><IoCloseCircle color='red' size={35}/></div>
        <h1 className={getStyle(styles.signupTitle, styles.darkSignupTitle)}>
          Đăng ký tài khoản{' '}
          <span className={getStyle(styles.signupLogo, styles.darkSignupLogo)}>
            Stocument
          </span>
        </h1>

        <form
          onSubmit={handleRegister}
          className={getStyle(styles.signupForm, styles.darkSignupForm)}
        >
          {/* Username */}
          <input
            type="text"
            placeholder="Tên đăng nhập"
            value={username}
            onChange={(e) => setUsername(e.target.value)}
            className={getStyle(styles.inputField, styles.darkInputField)}
            disabled={loading}
          />
          {usernameError && (
            <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>
              {usernameError}
            </p>
          )}

          {/* Full Name */}
          <input
            type="text"
            placeholder="Họ và tên"
            value={fullName}
            onChange={(e) => setFullName(e.target.value)}
            className={getStyle(styles.inputField, styles.darkInputField)}
            disabled={loading}
          />
          {fullNameError && (
            <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>
              {fullNameError}
            </p>
          )}

          {/* Email */}
          <input
            type="text"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className={getStyle(styles.inputField, styles.darkInputField)}
            disabled={loading}
          />
          {emailError && (
            <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>
              {emailError}
            </p>
          )}

          {/* Password */}
          <div className={styles.passwordWrapper}>
            <input
              type={isPasswordVisible ? 'text' : 'password'}
              placeholder="Mật khẩu"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className={getStyle(styles.inputField, styles.darkInputField)}
              disabled={loading}
            />
            <button
              type="button"
              onClick={togglePasswordVisibility}
              className={styles.togglePasswordBtn}
              aria-label={isPasswordVisible ? 'Ẩn mật khẩu' : 'Hiển thị mật khẩu'}
              disabled={loading}
            >
              {isPasswordVisible ? '🙈' : '🙉'}
            </button>
          </div>
          {passwordError && (
            <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>
              {passwordError}
            </p>
          )}

          {/* Confirm Password */}
          <div className={styles.passwordWrapper}>
            <input
              type={isConfirmVisible ? 'text' : 'password'}
              placeholder="Nhập lại mật khẩu"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              className={getStyle(styles.inputField, styles.darkInputField)}
              disabled={loading}
            />
            <button
              type="button"
              onClick={toggleConfirmVisibility}
              className={styles.togglePasswordBtn}
              aria-label={isConfirmVisible ? 'Ẩn mật khẩu' : 'Hiển thị mật khẩu'}
              disabled={loading}
            >
              {isConfirmVisible ?  '🙈' : '🙉'}
            </button>
          </div>
          {confirmPasswordError && (
            <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>
              {confirmPasswordError}
            </p>
          )}

          {/* Link đến Login */}
          <div
            className={getStyle(styles.signupOptions, styles.darkSignupOptions)}
            onClick={() => {
              props.setOpenSignUp(false);
              props.setOpenLogin(true);
            }}
            style={{ cursor: loading ? 'not-allowed' : 'pointer' }}
          >
            <a
              href="#"
              className={getStyle(styles.haveAccount, styles.darkHaveAccount)}
              onClick={(e) => e.preventDefault()}
            >
              Đã có tài khoản? Đăng nhập
            </a>
          </div>

          {/* Hiển thị lỗi chung do server trả về */}
          {serverError && (
            <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>
              {serverError}
            </p>
          )}

          {/* Nút submit hoặc loading message */}
          {loading ? (
            <p className={getStyle(styles.loadingMessage, styles.darkLoadingMessage)}>
              🔄 Đang đăng ký...
            </p>
          ) : (
            <button
              type="submit"
              className={getStyle(styles.signupBtn, styles.darkSignupBtn)}
            >
              Đăng ký
            </button>
          )}
        </form>
      </div>
    </div>
  );
}

export default RegisterForm;
