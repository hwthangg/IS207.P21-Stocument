import React, { createContext, useContext, useState } from 'react';

const HeaderContext = createContext();

export const HeaderProvider = ({ children }) => {
  const [activeLink, setActiveLink] = useState("Trang chủ");

  return (
    <HeaderContext.Provider value={{activeLink, setActiveLink}}>
      {children}
    </HeaderContext.Provider>
  );
};

// Custom hook
export const useHeader = () => useContext(HeaderContext);
