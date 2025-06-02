import React, { useEffect, useRef, useState, useCallback } from "react";
import styles from "./UploadFile.module.css";
import { MdOutlineFileUpload } from "react-icons/md";
import { useTheme } from "../../contexts/ThemeContext";
import { AiOutlineClose } from "react-icons/ai";
import { FaFilePdf } from "react-icons/fa";

function UploadFile() {
  const { theme } = useTheme();

  // State for dropdowns
  const [majors, setMajors] = useState([]);
  const [subjects, setSubjects] = useState([]);
  const [selectedMajor, setSelectedMajor] = useState("");
  const [selectedSubject, setSelectedSubject] = useState("");
  const [errors, setErrors] = useState({});

  // State for file upload
  const [isDragging, setIsDragging] = useState(false);
  const [uploadedFiles, setUploadedFiles] = useState([]);
  const fileInputRef = useRef(null);
  const dropAreaRef = useRef(null);

  // Fetch majors on component mount
  useEffect(() => {
    fetch("http://localhost/stocument/backend/majors.php")
      .then(res => res.json())
      .then(data => {
        if (data.success) setMajors(data.majors);
      })
      .catch(error => console.error("Error fetching majors:", error));
  }, []);

  // Fetch subjects when a major is selected
  useEffect(() => {
    if (!selectedMajor) {
      setSubjects([]);
      setSelectedSubject("");
      return;
    }
    fetch(
      `http://localhost/stocument/backend/subjects.php?major_id=${encodeURIComponent(
        selectedMajor
      )}`
    )
      .then(res => res.json())
      .then(data => {
        if (data.success) setSubjects(data.subjects);
      })
      .catch(error => console.error("Error fetching subjects:", error));
  }, [selectedMajor]);

  // Function to handle file appending and validation
  const appendFiles = useCallback(files => {
    const validFiles = Array.from(files).filter(file => {
      if (file.type !== "application/pdf") {
        alert(`❌ Chỉ cho phép tệp PDF: ${file.name}`);
        return false;
      }
      if (file.size > 100 * 1024 * 1024) { // 10MB limit
        alert(`❌ Tệp quá lớn (tối đa 10MB): ${file.name}`);
        return false;
      }
      return true;
    });

    const newFiles = validFiles.map((file, index) => ({
      id: `${file.name}-${Date.now()}-${index}`,
      file,
      name: file.name,
      url: URL.createObjectURL(file),
    }));

    setUploadedFiles(prevFiles => {
      const uniqueFiles = newFiles.filter(newFile =>
        !prevFiles.some(existingFile =>
          existingFile.name === newFile.name && existingFile.file.size === newFile.file.size
        )
      );
      return [...prevFiles, ...uniqueFiles];
    });
  }, [setUploadedFiles]);

  // Handlers for drag and drop functionality
  const handleDragOver = useCallback(e => {
    e.preventDefault();
    setIsDragging(true);
  }, []);

  const handleDragLeave = useCallback(e => {
    e.preventDefault();
    setIsDragging(false);
  }, []);

  const handleDrop = useCallback(e => {
    e.preventDefault();
    setIsDragging(false);
    appendFiles(e.dataTransfer.files);
  }, [appendFiles]);

  // Handler for file input change
  const handleFileChange = useCallback(e => {
    appendFiles(e.target.files);
  }, [appendFiles]);

  // Function to open the file dialog
  const openFileDialog = useCallback(() => {
    fileInputRef.current?.click();
  }, []);

  // Function to remove a file from the uploaded list
  const handleRemoveFile = useCallback(id => {
    setUploadedFiles(prevFiles => prevFiles.filter(file => file.id !== id));
  }, []);

  // Function to handle the upload process
  const handleUpload = useCallback(() => {
    const validationErrors = {};
    if (!selectedMajor) validationErrors.major = "Vui lòng chọn ngành.";
    if (!selectedSubject) validationErrors.subject = "Vui lòng chọn môn học.";
    if (uploadedFiles.length === 0) validationErrors.files = "Vui lòng chọn tệp.";
    setErrors(validationErrors);

    if (Object.keys(validationErrors).length > 0) return;

    const formData = new FormData();
    formData.append("major_id", selectedMajor);
    formData.append("subject_id", selectedSubject);
    uploadedFiles.forEach(fileItem => {
      formData.append("files[]", fileItem.file);
    });

    fetch("http://localhost/stocument/backend/upload.php", {
      method: "POST",
      body: formData,
      credentials: 'include',
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Tải lên thành công!");
          setUploadedFiles([]);
          setSelectedMajor("");
          setSelectedSubject("");
          setErrors({});
        } else {
          alert("Lỗi khi tải lên: " + data.message);
        }
      })
      .catch(error => console.error("Error during upload:", error));
  }, [selectedMajor, selectedSubject, uploadedFiles]);

  return (
    <div className={styles.uploadContainer}>
      <div
        ref={dropAreaRef}
        className={`${styles.dropArea} ${isDragging ? styles.dragActive : ""}`}
        onDragOver={handleDragOver}
        onDragLeave={handleDragLeave}
        onDrop={handleDrop}
        style={{
          borderColor: theme === "light" ? "#ccc" : "#555",
          backgroundColor: isDragging ? (theme === "light" ? "#f0f0f0" : "#444") : "transparent",
          color: theme === "light" ? "#333" : "#eee",
        }}
      >
        <div className={styles.uploadIcon}>
          <MdOutlineFileUpload size={60} color={theme === "light" ? "#6DD5BE" : "#602BF8"} />
        </div>
        <p className={styles.dragText}>Kéo và thả tệp PDF vào đây</p>
        <button
          type="button"
          onClick={openFileDialog}
          className={styles.browseButton}
          style={{ backgroundColor: theme === "light" ? "#EBEAE7" : "#38384A", color: theme === "light" ? "black" : "white" }}
        >
          Chọn tệp
        </button>
        <input
          type="file"
          accept="application/pdf"
          ref={fileInputRef}
          onChange={handleFileChange}
          multiple
          style={{ display: "none" }}
        />
      </div>

      <div className={styles.selectionControls}>
        <div className={styles.dropdown}>
          <label htmlFor="major" className={styles.label}>Ngành:</label>
          <select
            id="major"
            className={styles.selectField}
            value={selectedMajor}
            onChange={e => setSelectedMajor(e.target.value)}
          >
            <option value="">-- Chọn ngành --</option>
            {majors.map(major => (
              <option key={major.major_id} value={major.major_id}>
                {major.name}
              </option>
            ))}
          </select>
          {errors.major && <p className={styles.errorText}>{errors.major}</p>}
        </div>

        <div className={styles.dropdown}>
          <label htmlFor="subject" className={styles.label}>Môn học:</label>
          <select
            id="subject"
            className={styles.selectField}
            value={selectedSubject}
            onChange={e => setSelectedSubject(e.target.value)}
            disabled={!selectedMajor}
          >
            <option value="">-- Chọn môn --</option>
            {subjects.map(subject => (
              <option key={subject.subject_id} value={subject.subject_id}>
                {subject.name}
              </option>
            ))}
          </select>
          {errors.subject && <p className={styles.errorText}>{errors.subject}</p>}
        </div>
      </div>

      {uploadedFiles.length > 0 && (
        <div className={styles.uploadedFilesContainer}>
          <h3 className={styles.fileListTitle}>Tệp đã chọn:</h3>
          <ul className={styles.fileList}>
            {uploadedFiles.map(fileItem => (
              <li key={fileItem.id} className={styles.fileItem}>
                <FaFilePdf className={styles.fileIcon} />
                <span className={styles.fileName}>{fileItem.name}</span>
                <button
                  type="button"
                  onClick={() => handleRemoveFile(fileItem.id)}
                  className={styles.removeFileButton}
                  aria-label={`Xóa tệp ${fileItem.name}`}
                >
                  <AiOutlineClose />
                </button>
              </li>
            ))}
          </ul>
          {errors.files && <p className={styles.errorText}>{errors.files}</p>}
        </div>
      )}

      <button
        type="button"
        onClick={handleUpload}
        className={styles.uploadButton}
        style={{ backgroundColor: theme === "light" ? "#6DD5BE" : "#602BF8" }}
        disabled={uploadedFiles.length === 0 || !selectedMajor || !selectedSubject}
      >
        Tải lên
      </button>
    </div>
  );
}

export default UploadFile;