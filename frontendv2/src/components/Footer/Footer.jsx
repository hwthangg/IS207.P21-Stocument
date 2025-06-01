import { useEffect, useState } from "react";
import styles from "./Footer.module.css";
import {
  FaFacebookF,
  FaEnvelope,
  FaPhone,
  FaSun,
  FaMoon,
  FaInstagram,
  FaTwitter,
  FaMapMarkerAlt, // Added for Google Map link
} from "react-icons/fa";
import { useTheme } from "../../contexts/ThemeContext";
import { Link } from "react-router-dom"; // Removed 'data' as it was unused

const Footer = () => {
  const { theme, setTheme } = useTheme();
  const [contacts, setContacts] = useState({});

  const toggleTheme = (e) => {
    e.preventDefault();
    setTheme(theme === "dark" ? "light" : "dark");
    document.body.classList.toggle("dark-theme", theme === "light");
  };

  useEffect(() => {
    const fetchContacts = async () => {
      try {
        const res = await fetch(
          "http://localhost/stocument/backend/admin/ajax/information.php?get_contacts"
        );
        const data = await res.json();
        console.log(data); // Good for debugging
        setContacts(data);
      } catch (error) {
        console.error("Failed to fetch contact information:", error); // Log errors
      }
    };

    fetchContacts();
  }, [theme]);

  return (
    <footer
      className={styles.footer}
      style={
        theme === "light"
          ? { backgroundColor: "#d9d9d9", color: "black" }
          : { backgroundColor: "#333", color: "white" } // Darker background for dark theme
      }
    >
      <div className={styles.footerContainer}>
        {/* About / Contact Information Section */}
        <div className={`${styles.footerColumn} ${styles.footerAbout}`}>
          <div className={styles.footerLogo}>
            <svg
              width="60"
              height="60"
              viewBox="0 0 44 44"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M21.2074 19.3438C21.1278 18.5994 20.7926 18.0199 20.2017 17.6051C19.6165 17.1903 18.8551 16.983 17.9176 16.983C17.2585 16.983 16.6932 17.0824 16.2216 17.2812C15.75 17.4801 15.3892 17.75 15.1392 18.0909C14.8892 18.4318 14.7614 18.821 14.7557 19.2585C14.7557 19.6222 14.8381 19.9375 15.0028 20.2045C15.1733 20.4716 15.4034 20.6989 15.6932 20.8864C15.983 21.0682 16.304 21.2216 16.6562 21.3466C17.0085 21.4716 17.3636 21.5767 17.7216 21.6619L19.358 22.071C20.017 22.2244 20.6506 22.4318 21.2585 22.6932C21.8722 22.9545 22.4205 23.2841 22.9034 23.6818C23.392 24.0795 23.7784 24.5597 24.0625 25.1222C24.3466 25.6847 24.4886 26.3437 24.4886 27.0994C24.4886 28.1222 24.2273 29.0227 23.7045 29.8011C23.1818 30.5739 22.4261 31.179 21.4375 31.6165C20.4545 32.0483 19.2642 32.2642 17.8665 32.2642C16.5085 32.2642 15.3295 32.054 14.3295 31.6335C13.3352 31.2131 12.5568 30.5994 11.9943 29.7926C11.4375 28.9858 11.1364 28.0028 11.0909 26.8438H14.2017C14.2472 27.4517 14.4347 27.9574 14.7642 28.3608C15.0938 28.7642 15.5227 29.0653 16.0511 29.2642C16.5852 29.4631 17.1818 29.5625 17.8409 29.5625C18.5284 29.5625 19.1307 29.4602 19.6477 29.2557C20.1705 29.0455 20.5795 28.7557 20.875 28.3864C21.1705 28.0114 21.321 27.5739 21.3267 27.0739C21.321 26.6193 21.1875 26.2443 20.9261 25.9489C20.6648 25.6477 20.2983 25.3977 19.8267 25.1989C19.3608 24.9943 18.8153 24.8125 18.1903 24.6534L16.2045 24.142C14.767 23.7727 13.6307 23.2131 12.7955 22.4631C11.9659 21.7074 11.5511 20.7045 11.5511 19.4545C11.5511 18.4261 11.8295 17.5256 12.3864 16.7528C12.9489 15.9801 13.7131 15.3807 14.679 14.9545C15.6449 14.5227 16.7386 14.3068 17.9602 14.3068C19.1989 14.3068 20.2841 14.5227 21.2159 14.9545C22.1534 15.3807 22.8892 15.9744 23.4233 16.7358C23.9574 17.4915 24.233 18.3608 24.25 19.3438H21.2074Z"
                fill={theme === "light" ? "black" : "white"}
              />
              <path
                fillRule="evenodd"
                clipRule="evenodd"
                d="M4 0H25.6823C25.681 0.00653535 25.6798 0.0130758 25.6786 0.019621C26.1033 0.0465506 26.5203 0.222285 26.8448 0.546825L34.4608 8.16282C34.6082 8.31025 34.725 8.47675 34.811 8.65438C34.8742 8.65055 34.9373 8.64407 35 8.63493V9.38225V10.0029V40C35 42.2091 33.2091 44 31 44H4C1.79086 44 0 42.2091 0 40V4C0 1.79086 1.79086 0 4 0ZM32 12.0029H26C24.8954 12.0029 24 11.1075 24 10.0029V3H4C3.44771 3 3 3.44772 3 4V40C3 40.5523 3.44772 41 4 41H31C31.5523 41 32 40.5523 32 40V12.0029Z"
                fill={theme === "light" ? "black" : "white"}
              />
            </svg>
          </div>

          {/* Enhanced Contact Details */}
          <div className={styles.contactDetailsWrapper}>
            {contacts.address && (
              <div className={styles.contactItem}>
                <FaMapMarkerAlt size={20} className={styles.contactIcon} />
                <p>{contacts.address}</p>
              </div>
            )}

            {contacts.gmap && (
              <div className={styles.contactItem}>
                <FaMapMarkerAlt size={20} className={styles.contactIcon} />
                <a
                  href={contacts.gmap}
                  target="_blank"
                  rel="noopener noreferrer"
                  className={styles.contactLink}
                >
                  Liên kết Google Map
                </a>
              </div>
            )}

            {(contacts.pn1 || contacts.pn2) && (
              <div className={styles.contactItem}>
                <FaPhone size={20} className={styles.contactIcon} />
                <div className={styles.phoneNumbers}>
                  {contacts.pn1 && <p>{contacts.pn1}</p>}
                  {contacts.pn2 && <p>{contacts.pn2}</p>}
                </div>
              </div>
            )}

            {contacts.email && (
              <div className={styles.contactItem}>
                <FaEnvelope size={20} className={styles.contactIcon} />
                <p>{contacts.email}</p>
              </div>
            )}
          </div>
        </div>

        {/* Quick Access Links Section */}
        <div className={`${styles.footerColumn} ${styles.footerLinksGroup}`}>
          <h4
            style={theme === "light" ? { color: "black" } : { color: "white" }}
          >
            Truy cập nhanh
          </h4>
          <div className={styles.footerQuickLinks}>
            <Link to={"/"}>
              <p>Trang chủ</p>
            </Link>
            <Link to={"/about"}>
              <p>Giới thiệu</p>
            </Link>
            <Link to={"/document"}>
              <p>Tài liệu</p>
            </Link>
            <Link to={"/ask"}>
              <p>Đặt câu hỏi</p>
            </Link>
          </div>
        </div>

        {/* Contact Social Links & Google Map Iframe */}
        <div className={`${styles.footerColumn} ${styles.footerLinksGroup}`}>
          <h4
            style={theme === "light" ? { color: "black" } : { color: "white" }}
          >
            Liên hệ
          </h4>
          <ul className={styles.footerContactLinks}>
            <li>
              <a
                href={contacts.fb}
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Facebook"
              >
                <FaFacebookF size={25} />
              </a>
            </li>
            <li>
              <a
                href={contacts.insta}
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Instagram"
              >
                <FaInstagram size={25} />
              </a>
            </li>
            <li>
              <a
                href={contacts.tw}
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Twitter"
              >
                <FaTwitter size={25} />
              </a>
            </li>
          </ul>
          {contacts.iframe && (
            <iframe
              src={contacts.iframe}
              className={styles.googleMapIframe}
            ></iframe>
          )}
        </div>

        {/* Theme Toggle Section */}
        <div className={`${styles.footerColumn} ${styles.footerSetting}`}>
          <h4
            style={theme === "light" ? { color: "black" } : { color: "white" }}
          >
            Tùy chỉnh
          </h4>
          <a
            className={`${styles.settingToggle} ${
              theme === "dark" ? styles.active : ""
            }`}
            href="#"
            aria-label="Toggle Theme"
            role="switch"
            aria-checked={theme === "dark"}
            onClick={toggleTheme}
          >
            <div className={styles.toggleKnob}>
              {theme == "dark" ? (
                <>
                  {" "}
                  <FaMoon
                    className={`${styles.innerIcon} ${styles.moonIcon}`}
                  />
                </>
              ) : (
                <>
                  {" "}
                  <FaSun className={`${styles.innerIcon} ${styles.sunIcon}`} />
                </>
              )}
            </div>
          </a>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
