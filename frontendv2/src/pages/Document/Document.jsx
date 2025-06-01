import React, { useEffect, useState, useRef } from "react";
import styles from "./Document.module.css";
import { useTheme } from "../../contexts/ThemeContext";
import { useNavigate, useLocation } from "react-router-dom";
import { useHeader } from "../../contexts/HeaderContext";
import { useAuth } from "../../contexts/AuthContext.jsx";

const Document = () => {
  const { theme } = useTheme();
  const { setActiveLink } = useHeader();
  const { isLoggedIn } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const [documents, setDocuments] = useState([]);
  const [breadcrumbs, setBreadcrumbs] = useState([]);
  const [searchQuery, setSearchQuery] = useState("");
  const [searchResults, setSearchResults] = useState({
    folders: [],
    files: [],
  });
  const [showDropdown, setShowDropdown] = useState(false);
  const searchRef = useRef(null);

  const queryParams = new URLSearchParams(location.search);
  const path = queryParams.get("path") || "";

  useEffect(() => {
    setActiveLink("Tài liệu");

    fetch(
      `http://localhost/stocument/backend/list_documents.php?path=${encodeURIComponent(
        path
      )}`
    )
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          setDocuments(data.documents);
          setBreadcrumbs(data.breadcrumbs);
        } else {
          console.error("Lỗi API:", data.message);
        }
      });
  }, [path]);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (searchRef.current && !searchRef.current.contains(event.target)) {
        setShowDropdown(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, [theme]);

  const handleNavigate = (doc) => {
    setShowDropdown(false);
    if (doc.type === "folder") {
      navigate(`/document?path=${encodeURIComponent(doc.path)}`);
    } else {
      const query = new URLSearchParams({ path: doc.path, name: doc.name });
      navigate(`/view?${query.toString()}`);
    }
  };

  const renderIcon = (type) => {
    if (type === "folder") {
      return (
        <div style={{ display: "flex", justifyContent: "center" }}>
          <svg width="26" height="22" viewBox="0 0 26 22" fill="none">
            <rect
              width="26"
              height="13"
              rx="4"
              transform="matrix(-1 0 0 1 26 9)"
              fill={theme === "dark" ? "white" : "black"}
            />
            <path
              fillRule="evenodd"
              clipRule="evenodd"
              d="M22 4C24.2091 4 26 5.79086 26 8V11C26 8.79086 24.2091 7 22 7H4C1.79086 7 0 8.79086 0 11V8C0 5.79086 1.79086 4 4 4H22Z"
              fill={theme === "dark" ? "white" : "black"}
            />
            <path
              fillRule="evenodd"
              clipRule="evenodd"
              d="M15.1375 0H22C24.2091 0 26 1.79086 26 4V7H11V4L15.1375 0Z"
              fill={theme === "dark" ? "white" : "black"}
            />
          </svg>
        </div>
      );
    } else {
      return (
        <div style={{ display: "flex", justifyContent: "center" }}>
          <svg width="22" height="27" viewBox="0 0 22 27" fill="none">
            <path
              fillRule="evenodd"
              clipRule="evenodd"
              d="M4 0H16.452C16.4491 0.0239553 16.4467 0.0479526 16.4446 0.0719789C16.6014 0.125405 16.7486 0.213268 16.8739 0.335568L21.6611 5.00902L21.6651 5.01297C21.7767 5.01916 21.8887 5.01738 22 5.00761V5.36542C22 5.39499 21.9994 5.42441 21.9981 5.45366C21.9987 5.45359 21.9994 5.45353 22 5.45347V23C22 25.2091 20.2091 27 18 27H4C1.79086 27 0 25.2091 0 23V4C0 1.79086 1.79086 0 4 0ZM20 7.36542H17.0857C15.9811 7.36542 15.0857 6.46999 15.0857 5.36542V2.00301C15.0857 2.00201 15.0857 2.001 15.0857 2H4C2.89543 2 2 2.89543 2 4V23C2 24.1046 2.89543 25 4 25H18C19.1046 25 20 24.1046 20 23V7.36542Z"
              fill={theme === "dark" ? "white" : "black"}
            />
          </svg>
        </div>
      );
    }
  };

  const highlightMatch = (name) => {
    if (!searchQuery) return name;
    const regex = new RegExp(`(${searchQuery})`, "gi");
    return name.replace(regex, "<mark>$1</mark>");
  };

  const handleSearch = (e) => {
    const value = e.target.value;
    setSearchQuery(value);

    if (value.trim() === "") {
      setSearchResults({ folders: [], files: [] });
      setShowDropdown(false);
      return;
    }

    fetch("http://localhost/stocument/backend/search_document.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `query=${encodeURIComponent(value)}&path=${encodeURIComponent(
        path
      )}`,
    })
      .then((res) => res.json())
      .then((data) => {
        setSearchResults(data);
        setShowDropdown(true);
      })
      .catch((err) => console.error("Lỗi tìm kiếm:", err));
  };

  const renderDropdown = () => {
    const results = [...searchResults.folders, ...searchResults.files];
    if (!showDropdown || results.length === 0) return null;

    return (
       <div className={styles.dropdownResults}>
      {results.map((item, index) => {
        const type = item.url.includes("document.php") ? "folder" : "file";
        const rawPath = item.url.split("=")[1] || "";
        const itemPath = decodeURIComponent(rawPath.replace(/\+/g, " "));
        return (
          <div
            key={index}
            onClick={() => handleNavigate({ ...item, type, path: itemPath })}
            className={styles.dropdownItem}
            style={{ display: 'flex', alignItems: 'center', padding: '10px 16px', cursor: 'pointer', transition: 'background-color 0.15s ease-in-out' }}
          >
            <span className={styles.icon}>{renderIcon(type)}</span>
            <span
              className={styles.resultText}
              style={{ flex: 1, fontSize: '1rem', overflow: 'hidden', whiteSpace: 'nowrap', textOverflow: 'ellipsis' }}
              dangerouslySetInnerHTML={{ __html: highlightMatch(item.name) }}
            />
            <span className={styles.resultType} style={{ color: 'gray', fontSize: '0.9em', fontStyle: 'italic', marginLeft: '8px' }}>{type}</span>
          </div>
        );
      })}
    </div>
    );
  };

  return (
    <div
      className={`${styles.layout} ${theme === "dark" ? styles.darkTheme : ""}`}
    >
      <main className={styles.mainBody}>
        <div className={styles.mainBodyContainer}>
          <div className={styles.wrapperSearchDocument}>
            <h1 className={styles.documentTitle}>Tài liệu học tập</h1>
            <div className={styles.wrapperSearchUpload} ref={searchRef}>
              <div className={styles.searchContainer}><input
                type="text"
                className={styles.searchInput}
                placeholder="Tìm tài liệu..."
                value={searchQuery}
                onChange={handleSearch}
                onFocus={() => {
                  if (
                    searchResults.folders.length ||
                    searchResults.files.length
                  ) {
                    setShowDropdown(true);
                  }
                }}
              /> {renderDropdown()}</div>
              
              {isLoggedIn && (
                <button
                  className={styles.uploadButton}
                  onClick={() =>
                    navigate("/personal", { state: { isActived: 3 } })
                  }
                >
                  Tải lên
                </button>
              )}
             
            </div>
          </div>

          {/* <div className={styles.wrapperInstruction}>Tài liệu</div> */}

          <div className={styles.wrapperDocumentList}>
            {" "}
            <div className={styles.pathContainer}>
              <a onClick={() => navigate("/document")}>Tài liệu</a>
              {breadcrumbs.map((b, idx) => (
                <span key={idx}>
                  {" > "}
                  <a
                    onClick={() =>
                      navigate(`/document?path=${encodeURIComponent(b.path)}`)
                    }
                  >
                    {b.label}
                  </a>
                </span>
              ))}
            </div>
            <table className={styles.documentTable}>
              <thead>
                <tr>
                  <th style={{ textAlign: "center", width: "100px" }}>Loại</th>
                  <th style={{ paddingLeft: "20px" }}>Tên</th>
                  {/* <th></th> */}
                </tr>
              </thead>
              <tbody>
                <tr></tr>
                {documents.map((doc, index) => (
                  <tr
                    key={index}
                    onClick={() => handleNavigate(doc)}
                    style={{ borderBottom: "1px solid #ccc" }}
                  >
                    <td className={styles.iconCell}>{renderIcon(doc.type)}</td>
                    <td style={{ paddingLeft: "20px" }}>{doc.name}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  );
};

export default Document;
