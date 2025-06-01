import React, { useState, useEffect, useRef } from 'react';
import { Document, Page, pdfjs } from 'react-pdf';
import 'react-pdf/dist/esm/Page/AnnotationLayer.css';
import 'react-pdf/dist/esm/Page/TextLayer.css';
import styles from './OpenDocument.module.css';
import { useTheme } from '../../../contexts/ThemeContext';
import { useAuth } from '../../../contexts/AuthContext';
import { Link, useLocation } from 'react-router-dom';
import { useHeader } from '../../../contexts/HeaderContext';
import { FaBookmark, FaRegBookmark } from 'react-icons/fa';
import avatar from '../../../assets/avatar.png'

pdfjs.GlobalWorkerOptions.workerSrc = `//unpkg.com/pdfjs-dist@${pdfjs.version}/build/pdf.worker.min.mjs`;


const OpenDocument = () => {
  const [numPages, setNumPages] = useState(null);
  const [pageWidth, setPageWidth] = useState(null);
  const [comments, setComments] = useState([]);
  const [commentInput, setCommentInput] = useState('');
  const { theme } = useTheme();
  // const { isLoggedIn } = useAuth();
  const { setActiveLink } = useHeader();
  const { isLoggedIn, user } = useAuth();
  const location = useLocation();
  const pdfContainerRef = useRef(null);

  const queryParams = new URLSearchParams(location.search);
  const filePath = queryParams.get('path');
  const fileUrl = filePath
  ? `http://localhost/stocument/backend/fetch_pdf.php?path=${encodeURIComponent(filePath)}`
  : null;
  const getFileNameWithoutExtension = (path) => {
    if (!path) return '';
    const parts = path.split('/');
    const fileWithExt = parts[parts.length - 1];
    const fileNameOnly = fileWithExt.split('.').slice(0, -1).join('.');
    return fileNameOnly;
  };

  useEffect(() => {
    setActiveLink("Tài liệu");
  }, []);

  useEffect(() => {
    const updateWidth = () => {
      if (pdfContainerRef.current) {
        const width = pdfContainerRef.current.offsetWidth;
        const finalWidth = width > 768 ? width * 0.6 : width * 0.9;
        setPageWidth(Math.min(finalWidth, 1200));
      }
    };

    updateWidth();
    window.addEventListener('resize', updateWidth);
    return () => window.removeEventListener('resize', updateWidth);
  }, []);

  // useEffect(() => {
  //   console.log("📄 File URL đang tải:", fileUrl);
  // }, [fileUrl]);

  useEffect(() => {
    const checkBookmark = async () => {
      if (!filePath) return;
      try {
        const res = await fetch(`http://localhost/stocument/backend/check_bookmark.php?path=${encodeURIComponent(filePath)}`, {
          method: 'GET',
          credentials: 'include',
        });
        const data = await res.json();
        if (data.success) {
          setIsBookmarked(data.bookmarked);
        }
      } catch (err) {
        console.error("Lỗi khi kiểm tra bookmark:", err);
      }
    };
    checkBookmark();
  }, [filePath]);

  const fetchComments = async () => {
    try {
      const response = await fetch(`http://localhost/stocument/backend/get_comments.php?path=${encodeURIComponent(filePath)}`, {
        method: 'GET',
        credentials: 'include',
      });
      const data = await response.json();
      console.log(data)
      if (data.success) {
        setComments(data.comments);
        console.log('📥 Danh sách bình luận:', data.comments);
      } else {
        console.error('Không thể tải bình luận:', data.message);
      }
    } catch (error) {
      console.error('Lỗi khi tải bình luận:', error);
    }
  };

  useEffect(() => {
    if (filePath) {
      fetchComments();
    }
  }, [filePath]);

  const [isBookmarked, setIsBookmarked] = useState(false);

  const handleToggleBookmark = async () => {
    try {
      const response = await fetch(`http://localhost/stocument/backend/toggle_bookmark.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ path: filePath }),
      });

      const data = await response.json();
      if (data.success) {
        setIsBookmarked(data.bookmarked); // true or false
      } else {
        alert("❌ Lỗi: " + data.message);
      }
    } catch (error) {
      console.error('Lỗi khi gửi bookmark:', error);
    }
  };


  const handleSubmitComment = async () => {
    if (!commentInput.trim()) return;

    try {
      const response = await fetch('http://localhost/stocument/backend/add_comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          path: filePath,
          context: commentInput
        }),
      });

      const data = await response.json();
console.log(data)
      if (data.success) {
        setCommentInput('');
        await fetchComments(); // Tải lại toàn bộ danh sách từ server theo thứ tự mới nhất
      } else {
        alert('❌ Gửi bình luận thất bại: ' + data.message);
      }
    } catch (error) {
      console.error('Lỗi khi gửi bình luận:', error);
    }
  };

  const handleDeleteComment = async (commentId) => {
    const confirmed = window.confirm("Bạn có chắc chắn muốn xóa bình luận này?");
    if (!confirmed) return;

    try {
      const response = await fetch(`http://localhost/stocument/backend/delete_comment.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ comment_id: commentId })
      });

      const data = await response.json();
      if (data.success) {
        setComments(comments.filter(comment => comment.id !== commentId));
      } else {
        alert('❌ Xóa bình luận thất bại: ' + data.message);
      }
    } catch (error) {
      console.error('Lỗi khi xóa bình luận:', error);
    }
  };

  return (
    <div className={`${styles.layout} ${theme === 'dark' ? styles.darkTheme : ''}`}>
      <main className={styles.mainBody}>
        <div className={styles.wrapperInstruction}>
          <div className={styles.fileInfo}>
            <span className={styles.fileName}>Tài liệu: {getFileNameWithoutExtension(filePath) || 'Không có'}</span>
            {/* Nút bookmark */}
            {isLoggedIn && (
              <button className={styles.bookmarkButton} onClick={handleToggleBookmark} aria-label={isBookmarked ? 'Bỏ lưu tài liệu này' : 'Lưu tài liệu này'}>
  {isBookmarked ? (
    <>
      <FaBookmark size={30} className={styles.heartIcon} style={{ color: 'red' }} />
     
    </>
  ) : (
    <>
      <FaRegBookmark size={30}  />
     
    </>
  )}
</button>
            )}
            <button
              className={styles.downloadButton}
              onClick={async () => {
                if (!isLoggedIn) {
                  alert('⚠️ Bạn cần đăng nhập để tải xuống tài liệu.');
                  return;
                }

                try {
                  await fetch("http://localhost/stocument/backend/dowload_document.php", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ path: filePath })
                  });

                  // Sau khi cập nhật thành công, tạo và click thẻ <a> để tải
                  const link = document.createElement("a");
                  link.href = fileUrl;
                  link.download = getFileNameWithoutExtension(filePath) + ".pdf";
                  document.body.appendChild(link);
                  link.click();
                  document.body.removeChild(link);
                } catch (error) {
                  console.error("❌ Lỗi khi cập nhật lượt tải:", error);
                  alert("Lỗi khi cập nhật lượt tải.");
                }
              }}
            >
              Tải xuống
            </button>
          </div>
        </div>

        <div className={styles.wrapperDocumentDetail} ref={pdfContainerRef}>
          <div className={styles.pdfContainer}>
            {fileUrl && (
              <Document
                file={fileUrl}
                onLoadSuccess={({ numPages }) => setNumPages(numPages)}
                loading={<div>📄 Đang tải tài liệu...</div>}
                error={<div>❌ Không thể tải tài liệu. Kiểm tra lại đường dẫn hoặc định dạng.</div>}
              >
                {Array.from(new Array(numPages), (_, index) => (
                  <Page
                    key={`page_${index + 1}`}
                    pageNumber={index + 1}
                    width={pageWidth}
                    renderTextLayer={false}
                    renderAnnotationLayer={false}
                    className={styles.pdfPage}
                  />
                ))}
              </Document>
            )}
          </div>
        </div>

        {/* Bình luận */}
        <div className={styles.wrapperComment}>
          <h3>Bình luận</h3>
        </div>
        <div className={styles.wrapperCommentDetail}>
          {isLoggedIn ? (
            <>
              <div className={styles.inputWrapper}>
                <input
                  type="text"
                  className={styles.commentInput}
                  placeholder="Hãy nêu cảm nghĩ của bạn về tài liệu này"
                  value={commentInput}
                  onChange={(e) => setCommentInput(e.target.value)}
                />
                <button className={styles.submitButton} onClick={handleSubmitComment}>
                  Gửi
                </button>
              </div>
            </>
          ) : (
            <div className={styles.inputWrapper}>
              <p className={styles.loginRequired}>Hãy đăng nhập để bình luận</p>
            </div>
          )}
          <div className={styles.commentsList}>
            {comments.map(comment => (
              <div key={comment.id} className={styles.commentItem}>
                <img
                  src={comment.avatar || avatar}
                  alt="avatar"
                  className={styles.avatar}
                />
                <div className={styles.commentContent}>
                  <div className={styles.userNameRow}>
                    <span className={styles.userName}>{comment.user}</span>
                    {comment.user_id === user?.user_id && (
                      <button
                        onClick={() => handleDeleteComment(comment.id)}
                        className={styles.deleteButton}
                        title="Xóa bình luận"
                      >
                        🗑️ Xóa
                      </button>
                    )}
                  </div>
                  <div className={styles.userComment}>
                    {comment.text}
                    <div className={styles.commentTime}>
                      🕒 {new Date(comment.created_at).toLocaleString('vi-VN')}
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        <Link to="/document">
          <div className={styles.wrapperTurnBack}>
            <p>⬅ Trở lại danh sách tài liệu</p>
          </div>
        </Link>
      </main>
    </div>
  );
};

export default OpenDocument;
