import React, { useEffect, useRef, useState } from "react";
import styles from "../PDFItem/PDFItem.module.css";
import { Document, Page, pdfjs } from "react-pdf";
import { useTheme } from "../../contexts/ThemeContext";
import { useNavigate } from "react-router-dom";
import { AiOutlineCalendar } from "react-icons/ai";

pdfjs.GlobalWorkerOptions.workerSrc = `//unpkg.com/pdfjs-dist@${pdfjs.version}/build/pdf.worker.min.mjs`;

function PDFBookmark({ fileUrl, originPath, title, docId, uploadedAt, onDeleted }) {
  const { theme } = useTheme();
  const [widthItem, setWidthItem] = useState(0);
  const card = useRef(null);
  const navigate = useNavigate();
  const [isHovered, setIsHovered] = useState(false);

  useEffect(() => {
    const updateSize = () => {
      if (card.current) {
        setWidthItem(card.current.offsetWidth);
      }
    };
    updateSize();
    window.addEventListener("resize", updateSize);
    return () => window.removeEventListener("resize", updateSize);
  }, []);

  const handleClick = () => {
    const query = new URLSearchParams();
    query.set("path", originPath);
    navigate(`/view?${query.toString()}`);
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return date.toLocaleDateString('vi-VN', options);
  };

  return (
    <div className={theme == 'dark' ? styles.darkTheme :''}> <div
      className={styles.container_card}
      ref={card}
      onClick={handleClick}
      style={{ cursor: 'pointer' }}
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
    >
      <div className={styles.thumbnail_container}>
        <Document file={fileUrl}>
          <Page
            pageNumber={1}
            renderTextLayer={false}
            renderAnnotationLayer={false}
            width={widthItem}
          />
        </Document>
      </div>

      <div
        className={`${styles.card_info} ${isHovered ? styles.card_info_hovered : ''}`}
        style={{
          backgroundColor: theme === 'light' ? '#F9FAFB' : '#374151',
          color: theme === 'light' ? '#1F2937' : '#F3F4F6',
        }}
      >
        <p className={styles.card_title} title={title}>
          {title.length > 25 ? `${title.slice(0, 25)}...` : title}
        </p>
        <div className={styles.card_meta}>
          <span title={`Đã lưu ${formatDate(uploadedAt)}`}>
            <AiOutlineCalendar className={styles.icon} /> Đã lưu: {formatDate(uploadedAt)}
          </span>
        </div>
      </div>
    </div></div>
   
  );
}

export default PDFBookmark;