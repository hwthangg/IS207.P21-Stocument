import { useState } from "react";
import { Send } from 'lucide-react';
import styles from './AskQuestion.module.css';
import { useTheme } from "../../contexts/ThemeContext.jsx";
import { useNavigate } from "react-router-dom";
const AskQuestionPage = ({ onNavigate }) => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    title: '',
    content: '',
  });
  const {theme} = useTheme()
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [serverError, setServerError] = useState('');

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prevState => ({
      ...prevState,
      [name]: value
    }));
  };
const navigate = useNavigate()
  const handleSubmit = async (e) => {
    e.preventDefault();
    setServerError('');
    setIsLoading(true);

    try {
      const res = await fetch(
        'http://localhost/stocument/backend/ask_question.php',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          credentials: 'include',
          body: JSON.stringify({
            name: formData.name,
            email: formData.email,
            subject: formData.title,
            message: formData.content
          }),
        }
      );

      if (!res.ok) {
        throw new Error(`HTTP lỗi: ${res.status}`);
      }

      const data = await res.json();
      if (data.success) {
        setIsSubmitted(true);
        setFormData({ name: '', email: '', title: '', content: '' });
      } else {
        setServerError(data.message || 'Đã xảy ra lỗi khi gửi câu hỏi.');
      }
    } catch (err) {
      console.error('Lỗi khi gửi yêu cầu:', err);
      setServerError('Lỗi máy chủ hoặc kết nối. Vui lòng thử lại.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
       <div className={theme === "dark" ? styles.darkTheme : ""}> <div className={styles.askQuestionContainer}>
      <div className={styles.questionCard}>
        <div className={styles.questionHeader}>
          <h1 className={styles.questionTitle}>Đặt câu hỏi</h1>
          <p className={styles.questionSubtitle}>Chúng tôi sẽ phản hồi bạn sớm nhất có thể.</p>
        </div>

        {isSubmitted ? (
          <div className={styles.successContainer}>
            <h3 className={styles.successTitle}>Gửi câu hỏi thành công!</h3>
            <p className={styles.successMessage}>
              Cảm ơn bạn đã liên hệ. Chúng tôi sẽ xem xét câu hỏi của bạn và phản hồi sớm.
            </p>
            <button
              onClick={() => navigate('/')}
              className={styles.homeButton}
            >
              Về trang chủ
            </button>
          </div>
        ) : (
          <form
            onSubmit={handleSubmit}
            className={styles.questionForm}
          >
            {[
              { id: 'name', label: 'Họ tên', placeholder: 'Nguyễn Văn A', type: 'text' },
              { id: 'email', label: 'Email', placeholder: 'you@example.com', type: 'email' },
              { id: 'title', label: 'Tiêu đề', placeholder: 'Về vấn đề...', type: 'text' },
            ].map(field => (
              <div key={field.id} className={styles.formGroup}>
                <label
                  htmlFor={field.id}
                  className={styles.formLabel}
                >
                  {field.label} <span className={styles.requiredField}>*</span>
                </label>
                <input
                  type={field.type}
                  name={field.id}
                  id={field.id}
                  value={formData[field.id]}
                  onChange={handleChange}
                  required
                  placeholder={field.placeholder}
                  disabled={isLoading}
                  className={styles.formInput}
                />
              </div>
            ))}

            <div className={styles.formGroup}>
              <label
                htmlFor="content"
                className={styles.formLabel}
              >
                Nội dung câu hỏi <span className={styles.requiredField}>*</span>
              </label>
              <textarea
                name="content"
                id="content"
                value={formData.content}
                onChange={handleChange}
                required
                placeholder="Nội dung chi tiết câu hỏi của bạn..."
                disabled={isLoading}
                className={styles.formTextarea}
              ></textarea>
            </div>

            {serverError && (
              <p className={styles.errorMessage}>
                {serverError}
              </p>
            )}

            <div className={styles.buttonGroup}>
              <button
                type="submit"
                disabled={isLoading}
                className={styles.submitButton}
              >
                {isLoading ? (
                  <>
                    <svg
                      className={styles.spinner}
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        className={styles.spinnerCircle}
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        strokeWidth="4"
                      ></circle>
                      <path
                        className={styles.spinnerPath}
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      ></path>
                    </svg>
                    Đang gửi...
                  </>
                ) : (
                  <>
                    <Send size={18} style={{ marginRight: '0.5rem' }} />
                    Gửi câu hỏi
                  </>
                )}
              </button>
            </div>
          </form>
        )}
      </div>
    </div></div>
   
  );
};

export default AskQuestionPage;