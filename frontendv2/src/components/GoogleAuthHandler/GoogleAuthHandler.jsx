// GoogleAuthHandler.jsx
import { useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

function GoogleAuthHandler() {
  const { setIsLoggedIn, setUser } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  useEffect(() => {
    const params = new URLSearchParams(location.search);
    const loginStatus = params.get('googleLogin');

    if (loginStatus === 'success') {
      const user = {
        user_id: params.get('user_id'),
        username: params.get('username'),
        full_name: params.get('full_name'),
        role: params.get('role'),
      };

      // Lưu thông tin đăng nhập
      localStorage.setItem('user', JSON.stringify(user));
      localStorage.setItem('isLoggedIn', 'true');

      setIsLoggedIn(true);
      setUser(user);

      navigate('/'); // hoặc /personal nếu muốn
    } else {
      alert('❌ Đăng nhập Google thất bại.');
      navigate('/login');
    }
  }, []);

  return null;
}

export default GoogleAuthHandler;
