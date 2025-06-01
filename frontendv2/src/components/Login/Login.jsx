import React, { useState, useEffect } from 'react';
import { AiOutlineEye, AiOutlineEyeInvisible } from 'react-icons/ai';
import { IoCloseCircle } from "react-icons/io5";
import styles from './Login.module.css';
import { useAuth } from '../../contexts/AuthContext';
import { useTheme } from '../../contexts/ThemeContext';
import { FcGoogle } from "react-icons/fc";

function Login(props) {
  const [emailOrUsername, setEmailOrUsername] = useState('');
  const [password, setPassword] = useState('');
  const [emailOrUsernameError, setEmailOrUsernameError] = useState('');
  const [passwordError, setPasswordError] = useState('');
  const [isPasswordVisible, setIsPasswordVisible] = useState(false);

  const { setIsLoggedIn, setUser } = useAuth();
  const { theme } = useTheme();

  const getStyle = (baseStyle, darkStyle) => {
    return theme === 'dark' ? `${baseStyle} ${darkStyle}` : baseStyle;
  };

  const togglePasswordVisibility = () => {
    setIsPasswordVisible(prev => !prev);
  };

  const handleLogin = (e) => {
    e.preventDefault();

    let isValid = true;

    if (!emailOrUsername.trim()) {
      setEmailOrUsernameError("Vui lòng nhập email hoặc tên đăng nhập.");
      isValid = false;
    } else {
      setEmailOrUsernameError('');
    }

    if (!password) {
      setPasswordError("Vui lòng nhập mật khẩu.");
      isValid = false;
    } else if (password.length < 8) {
      setPasswordError("Mật khẩu phải có ít nhất 8 ký tự.");
      isValid = false;
    } else {
      setPasswordError('');
    }

    if (isValid) {
      fetch('http://localhost/stocument/backend/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({
          username_or_email: emailOrUsername,
          password
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {

            const user = {
              user_id: data.user.user_id,
              username: data.user.username,
              role: data.user.role,
              fullName: data.user.full_name
            }
            console.log(user);
        
            localStorage.setItem('user', JSON.stringify(data.user));
            localStorage.setItem('isLoggedIn', 'true');
                setIsLoggedIn(true);
          
      setUser(prev => ({...prev,user}));
            props.setOpenLogin(false);
          } else {
            alert("Lỗi: " + data.message);
          }
        })
        .catch(error => {
          console.error("Lỗi kết nối:", error);
          alert("Lỗi máy chủ hoặc mạng.");
        });
    }
  };

  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const googleSuccess = params.get('googleLogin');
    const email = params.get('email');

    if (googleSuccess === 'success' && email) {
      console.log("🎉 Google Login thành công với email:", email);
      // Có thể gọi thêm API hoặc cập nhật context tại đây
    }
  }, []);

  return (
    <div className={theme === 'dark' ? styles.darkTheme : ''}>
      <div className={getStyle(styles.layout, styles.darkLayout)}>
        <div className={getStyle(styles.loginWrapper, styles.darkLoginWrapper)} style={{ position: 'relative' }}>
          <div style={{ position: 'absolute', right: 0, top: 0 }} onClick={() => props.setOpenLogin(false)}>
            <IoCloseCircle color='red' size={35} />
          </div>
          <h1 className={getStyle(styles.loginTitle, styles.darkLoginTitle)}>
            Đăng nhập vào <span className={getStyle(styles.loginLogo, styles.darkLoginLogo)}>Stocument</span>
          </h1>
          <form onSubmit={handleLogin} className={getStyle(styles.loginForm, styles.darkLoginForm)}>
            <input
              type="text"
              placeholder="Email / Tên đăng nhập"
              value={emailOrUsername}
              onChange={(e) => setEmailOrUsername(e.target.value)}
              className={getStyle(styles.inputField, styles.darkInputField)}
            />
            {emailOrUsernameError && <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>{emailOrUsernameError}</p>}

            <div className={styles.passwordWrapper}>
              <input
                type={isPasswordVisible ? 'text' : 'password'}
                placeholder="Mật khẩu"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className={getStyle(styles.inputField, styles.darkInputField)}
              />
              <button
                type="button"
                onClick={togglePasswordVisibility}
                className={styles.togglePasswordBtn}
                aria-label={isPasswordVisible ? 'Ẩn mật khẩu' : 'Hiển thị mật khẩu'}
              >
                {isPasswordVisible ? '🙈' : '🙉'}
              </button>
            </div>
            {passwordError && <p className={getStyle(styles.errorMessage, styles.darkErrorMessage)}>{passwordError}</p>}

            <div className={getStyle(styles.loginOptions, styles.darkLoginOptions)}>
              <a href="#" className={getStyle(styles.forgotPassword, styles.darkForgotPassword)} onClick={() => { props.setOpenForgot(true); props.setOpenLogin(false); }}>Quên mật khẩu?</a>
              <a href="#" className={getStyle(styles.noAccount, styles.darkNoAccount)} onClick={() => { props.setOpenSignUp(true); props.setOpenLogin(false); }}>Chưa có tài khoản?</a>
            </div>

            <button type="submit" className={getStyle(styles.loginBtn, styles.darkLoginBtn)}>
              Đăng nhập
            </button>

            <button
              type="button"
              onClick={() => {
                window.location.href = 'http://localhost/stocument/backend/google_login.php';
              }}
              className={styles.googleLoginBtn}
            >
              <FcGoogle size={50}/>
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}

export default Login;
