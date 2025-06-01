import React, { createContext, useContext, useState, useEffect } from 'react';


const AuthContext = createContext();


export const AuthProvider = ({ children }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [user, setUser] = useState(null);           

    // Tải lại thông tin từ localStorage khi component mount
  useEffect(() => {
    const savedUser = localStorage.getItem('user');
    const savedLoginStatus = localStorage.getItem('isLoggedIn');

    if (savedUser && savedLoginStatus === 'true') {
     
      setUser(JSON.parse(savedUser));
      setIsLoggedIn(true);
    }
  }, [isLoggedIn]);

  return (
    <AuthContext.Provider value={{
      isLoggedIn,
      setIsLoggedIn,
      user,           
      setUser
    }}>
      {children}
    </AuthContext.Provider>
  );
};
// Custom hook
export const useAuth = () => useContext(AuthContext);