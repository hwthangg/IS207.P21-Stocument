import React, { useEffect, useRef, useState } from "react";
import styles from "../PDFItem/PDFItem.module.css";
import { Document, Page, pdfjs } from "react-pdf";
import { useTheme } from "../../contexts/ThemeContext";
import { useNavigate } from "react-router-dom";
import { AiOutlineDownload, AiOutlineDelete, AiOutlineCalendar, AiFillDelete } from "react-icons/ai";

pdfjs.GlobalWorkerOptions.workerSrc = `//unpkg.com/pdfjs-dist@${pdfjs.version}/build/pdf.worker.min.mjs`;

function PDFItem({ fileUrl, originPath, title, downloadCount, docId, uploadedAt, onDeleted }) {
  const { theme } = useTheme();
  const [widthItem, setWidthItem] = useState(0);
  const card = useRef(null);
  const navigate = useNavigate();


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

  const handleDelete = async (e) => {
    e.stopPropagation(); // Ngăn không redirect khi bấm nút xoá

    if (!window.confirm("Bạn có chắc muốn xoá tài liệu này?")) return;

    try {
      const response = await fetch("http://localhost/stocument/backend/delete_document.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ doc_id: docId }),
      });

      const result = await response.json();
      if (result.success) {
        alert("✅ Tài liệu đã được xoá.");
        onDeleted(docId); // Gọi callback để cập nhật danh sách
      } else {
        alert("❌ Xoá thất bại: " + result.message);
      }
    } catch (err) {
      console.error("Lỗi khi xoá:", err);
      alert("⚠️ Lỗi hệ thống khi xoá.");
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return date.toLocaleDateString('vi-VN', options);
  };

  return (
    <div className={theme == 'dark'?styles.darkTheme :''}> <div
      className={styles.container_card}
      ref={card}
      onClick={handleClick}
      style={{ cursor: 'pointer' }}
     
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
        className={`${styles.card_info}`}
        style={{
          backgroundColor: theme === 'light' ? '#F9FAFB' : '#374151',
          color: theme === 'light' ? '#1F2937' : '#F3F4F6',
        }}
      >
        <p className={styles.card_title} title={title}>
          {title.length > 25 ? `${title.slice(0, 25)}...` : title}
        </p>
        <div className={styles.card_meta}>
          <span>
            <AiOutlineDownload className={styles.icon} /> {downloadCount}
          </span>
          <span title={`Đã tải lên ${formatDate(uploadedAt)}`}>
            <AiOutlineCalendar className={styles.icon} /> Đã tải lên: {formatDate(uploadedAt)}
          </span>
        </div>
        <button onClick={handleDelete} className={styles.delete_button} aria-label="Xoá tài liệu">
          <AiFillDelete  size={30}/>
        </button>
      </div>
    </div></div>
   
  );
}

export default PDFItem;