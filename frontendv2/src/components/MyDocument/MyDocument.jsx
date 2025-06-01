// src/pages/MyDocument/MyDocument.jsx
import React, { useEffect, useState } from "react";
import PDFItem from "../../components/PDFItem/PDFItem";
import PDFBookmark from "../../components/PDFBookmark/PDFBookmark";
import styles from "./MyDocument.module.css";
import { useTheme } from "../../contexts/ThemeContext";

function MyDocument() {
  const [docs, setDocs] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isBookmark, setIsBookmark] = useState(true);
  const [error, setError] = useState("");
  const [snackbarOpen, setSnackbarOpen] = useState(false);
  const {theme} = useTheme()
  useEffect(() => {
    setLoading(true);
    setError("");

    const url = isBookmark
      ? "http://localhost/stocument/backend/my_document.php"
      : "http://localhost/stocument/backend/my_bookmark.php";

    fetch(url, {
      credentials: "include",
    })
      .then((res) => {
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
      })
      .then((data) => {
        if (data.success) {
          if (isBookmark) {
            setDocs(data.documents || []);
          } else {
            setDocs(data.bookmarks || []);
          }
        } else {
          setError(
            `Lỗi lấy ${isBookmark ? "tài liệu" : "bookmark"}: ${data.message}`
          );
          setSnackbarOpen(true);
          if (isBookmark) {
            setDocs([]);
          } else {
            setDocs([]);
          }
        }
      })
      .catch((err) => {
        console.error("Fetch error:", err);
        setError(`Lỗi kết nối khi lấy ${isBookmark ? "tài liệu" : "bookmark"}`);
        setSnackbarOpen(true);
        if (isBookmark) {
          setDocs([]);
        } else {
          setDocs([]);
        }
      })
      .finally(() => {
        setLoading(false);
      });
  }, [isBookmark]);

  const handleSnackbarClose = () => {
    setSnackbarOpen(false);
  };

  return (
    <div className={theme == 'dark' ? styles.darkTheme : ''}> <div className={styles.gridContainer}>
      <div className={styles.header}>
        <div>
          <button
            onClick={() => setIsBookmark(true)}
            className={isBookmark ? styles.activeTab : styles.inactiveTab}
          >
            Tải lên
          </button>
          <button
            onClick={() => setIsBookmark(false)}
            className={!isBookmark ? styles.activeTab : styles.inactiveTab}
          >
            Đã lưu
          </button>
        </div>
      </div>

      {loading ? (
        <div className={styles.loadingContainer}>
          <p>Đang tải {isBookmark ? "tài liệu" : "bookmark"}...</p>
        </div>
      ) : docs.length === 0 ? (
        <div className={styles.emptyState}>
          <p>
            Chưa có {isBookmark ? "tài liệu nào được tải lên" : "tài liệu nào được bookmark"}.
          </p>
        </div>
      ) : (
        <div className={styles.gridViewer}>
          {docs.map((item) =>
            isBookmark ? (
              <PDFItem
                key={item.doc_id}
                fileUrl={`http://localhost/stocument/backend/fetch_pdf.php?path=${encodeURIComponent(
                  item.file_path_subject
                )}`}
                originPath={item.file_path_subject}
                title={item.title}
                downloadCount={item.download_count}
                docId={item.doc_id}
                uploadedAt={item.uploaded_at}
                onDeleted={(deletedId) =>
                  setDocs((prev) => prev.filter((d) => d.doc_id !== deletedId))
                }
              />
            ) : (
              <PDFBookmark
                key={item.doc_id}
                fileUrl={`http://localhost/stocument/backend/fetch_pdf.php?path=${encodeURIComponent(
                  item.file_path_subject
                )}`}
                originPath={item.file_path_subject}
                title={item.title}
                uploadedAt={item.bookmarked_at}
              />
            )
          )}
        </div>
      )}

      {snackbarOpen && (
        <div className={styles.snackbar}>
          <p className={styles.snackbarMessage}>{error}</p>
          <button onClick={handleSnackbarClose} className={styles.snackbarCloseButton}>
            Đóng
          </button>
        </div>
      )}
    </div></div>
   
  );
}

export default MyDocument;