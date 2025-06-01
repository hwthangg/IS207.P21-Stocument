import React, { useEffect, useRef, useState } from "react";
import styles from "../Personal/Personal.module.css";
import statisticIcon from "../../assets/statistic.png";
import docsIcon from "../../assets/docs.png";
import uploadIcon from "../../assets/upload.png";
import MyDocument from "../../components/MyDocument/MyDocument.jsx";
import Statistic from "../../components/Statistic/Statistic.jsx";
import UploadFile from "../../components/UploadFile/UploadFile.jsx";

import { useTheme } from "../../contexts/ThemeContext.jsx";
import { useHeader } from "../../contexts/HeaderContext.jsx";
import { useLocation } from "react-router-dom";

function Personal() {
  const location = useLocation()
  const {theme} = useTheme()
  const {setActiveLink} = useHeader()
  const [isActived, setActived] = useState(1); // trạng thái item được chọn
  const activeRef = useRef(null); // ref cho thanh activeIndicator
  const itemRefs = [useRef(null), useRef(null), useRef(null)]; // ref cho từng item
  const [title, setTitle] = useState("Danh sách các tài liệu đã tải lên"); // tiêu đề động

  useEffect(()=>{if(location.state?.isActived != undefined){
    setActived(3)
  }},[location.state])
  useEffect(() => {
    console.log(theme)
    const targetItem = itemRefs[isActived - 1]?.current;
    const activeItem = activeRef.current;

    // Di chuyển thanh activeIndicator theo item được chọn
    if (targetItem && activeItem) {
      const offsetTop = targetItem.offsetTop;
      activeItem.style.top = `${offsetTop}px`;
    }

    // Đổi tiêu đề dựa theo item được chọn
    switch (isActived) {
      case 1:
        setTitle("Tài liệu của bạn");
        break;
      case 2:
        setTitle("Số liệu");
        break;
      case 3:
        setTitle("Tải lên");
        break;
    }
  }, [isActived]);

useEffect(()=>{
  setActiveLink("")
},[])

  return (
    <div className={styles.container} style={{backgroundColor: theme == 'light' ? '#FDFAF5' : '#1B1C31'}}>
      <div className={styles.contentWrapper} style={{backgroundColor: theme == 'light' ? 'white' : '#46285140'}}>
        {/* Sidebar bên trái */}
        <div className={styles.sidebar} style={{backgroundColor: theme == 'light' ? '#D9D9D9' : '#464756'}} >
          <div
            className={styles.sidebarItem}
            ref={itemRefs[0]}
            onClick={() => setActived(1)}
          >
            <img src={docsIcon} alt="option" />
          </div>
          <div
            className={styles.sidebarItem}
            ref={itemRefs[1]}
            onClick={() => setActived(2)}
          >
            <img src={statisticIcon} alt="option" />
          </div>
          <div
            className={styles.sidebarItem}
            ref={itemRefs[2]}
            onClick={() => setActived(3)}
          >
            <img src={uploadIcon} alt="option" />
          </div>
          {/* Thanh di chuyển theo item được chọn */}
          <div className={styles.activeIndicator} ref={activeRef} style={{backgroundColor: theme == 'light' ? '#6DD5BE' : '#602BF8'}}></div>
        </div>

        {/* Nội dung chính bên phải */}
        <div className={styles.mainContent}>
          <p style={{color: theme == 'light' ? 'black' : 'white'}}>{title}</p>
          <div className={styles.contentBox}>
            {/* Nội dung động hiển thị ở đây */}
          
           {isActived == 1 ? <MyDocument/> :<></>}
           {isActived == 2 ? <Statistic/> :<></>}
           {isActived == 3 ? <UploadFile/> :<></>}
          </div>
        </div>
      </div>
      
    </div>
  );
}

export default Personal;
