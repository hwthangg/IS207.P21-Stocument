// App.jsx
import { useEffect, useState } from 'react';
import { Routes, Route, useLocation } from 'react-router-dom';

import Header from './components/Header/Header.jsx';
import Footer from './components/Footer/Footer.jsx';
import Home from './pages/Home/Home.jsx';
import About from './pages/About/About.jsx';
import Document from './pages/Document/Document.jsx';
import OpenDocument from './pages/Document/OpenDocument/OpenDocument.jsx';
import Personal from './pages/Personal/Personal.jsx';
import Login from './components/Login/Login.jsx';
import ResetPopup from './components/ResetPopup/ResetPopup.jsx';
import VerifyPopup from './components/VerifyPopup/VerifyPopup.jsx';
import AskQuestionPage from './pages/AskQuestion/AskQuestion.jsx';
import GoogleAuthHandler from './components/GoogleAuthHandler/GoogleAuthHandler.jsx'; 
function AppRoutes() {
  const location = useLocation();
  const params = new URLSearchParams(location.search);
  const resetToken = params.get('resetToken');
  const verifyToken = params.get('verifyToken');

  const [showResetPopup, setShowResetPopup] = useState(false);
  const [showVerifyPopup, setShowVerifyPopup] = useState(false);


  useEffect(() => {
    if (resetToken) {
      setShowResetPopup(true);
    }
  }, [resetToken]);
  
  useEffect(() => {
      if (verifyToken) {
        setShowVerifyPopup(true);
      }
    }, [verifyToken]);

  return (
    <>
      <Header />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<Login />} />
        <Route path="/personal" element={<Personal />} />
        <Route path="/about" element={<About />} />
        <Route path="/document" element={<Document />} />
        <Route path="/ask" element={<AskQuestionPage />} />
        <Route path="/view" element={<OpenDocument />} />
        <Route path="/open-document" element={<OpenDocument />} />
        <Route path="/google-auth" element={<GoogleAuthHandler />} />
      </Routes>
      
      {showResetPopup && (
        <ResetPopup token={resetToken} onClose={() => setShowResetPopup(false)} />
      )}
      {showVerifyPopup && (
        <VerifyPopup token={verifyToken} onClose={() => setShowVerifyPopup(false)} />
      )}

      <Footer />
    </>
  );
}

export default AppRoutes;
