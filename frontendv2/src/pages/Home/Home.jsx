import React, { useState, useEffect } from "react";
import styles from "./Home.module.css";
import { useTheme } from "../../contexts/ThemeContext.jsx";
import { Link } from "react-router-dom";
import { useHeader } from "../../contexts/HeaderContext.jsx";
import img1 from "../../assets/img1.png";
import img2 from "../../assets/img2.png";
import img3 from "../../assets/img3.png";

const Home = () => {
  const { setActiveLink } = useHeader();
  const { theme } = useTheme();
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [general, setGeneral] = useState({});
  const [contacts, setContacts] = useState({});

  const images = [
    { src: img1, alt: "Ảnh 1" },
    { src: img2, alt: "Ảnh 2" },
    { src: img3, alt: "Ảnh 3" },
  ];

  const updateCarousel = (index) => {
    setCurrentImageIndex(index);
  };

  const handleSlide = (direction) => {
    const newIndex =
      (currentImageIndex + direction + images.length) % images.length;
    updateCarousel(newIndex);
  };

  useEffect(() => {
    const fetchGeneral = async () => {
      try {
        const res = await fetch(
          "http://localhost/stocument/backend/admin/ajax/information.php?get_general"
        );
        const data = await res.json();
        setGeneral(data);
      } catch (error) {
        console.error("Error fetching general data:", error);
      }
    };

    const fetchContacts = async () => {
      try {
        const res = await fetch(
          "http://localhost/stocument/backend/admin/ajax/information.php?get_contacts"
        );
        const data = await res.json();
        setContacts(data);
      } catch (error) {
        console.error("Error fetching contacts data:", error);
      }
    };

    fetchGeneral();
    fetchContacts();
    setActiveLink("Trang chủ");
  }, [setActiveLink]);

  useEffect(() => {

  }, [theme]);

  return (
    <div className={theme === "dark" ? styles.darkTheme : ""}>
      <section className={styles.mainbody}>
        <div className={styles.mainbodyContainer}>
          <div className={styles.heroleft}>
            <h1 className={styles.heroTitle}>{general.site_title}</h1>
            <p className={styles.heroSubtitle}>
              Nền tảng chia sẻ tài liệu miễn phí
            </p>
            <p className={styles.heroDescription}>{general.site_about}</p>
            <Link to={"/about"} onClick={() => setActiveLink("Giới thiệu")}>
              <div className={styles.ctaButton}>
                <div><p>Khám phá ngay</p></div>
                
              </div>
            </Link>
          </div>

          <div className={styles.heroright}>
            <div className={styles.imageCarousel}>
              <div className={styles.imageStack}>
                {images.map((image, index) => {
                  const position =
                    (index - currentImageIndex + images.length) % images.length;
                  return (
                    <img
                      key={index}
                      className={`${styles.imgCard} ${
                        styles[`pos${position}`]
                      }`}
                      src={image.src}
                      alt={image.alt}
                      onClick={() => {
                        updateCarousel(index), handleSlide(1);
                      }}
                    />
                  );
                })}
              </div>
            </div>

            <div className={styles.dotGroup}>
              {images.map((_, index) => (
                <div
                  key={index}
                  className={`${styles.dot} ${
                    currentImageIndex === index ? styles.selected : ""
                  }`}
                  onClick={() => updateCarousel(index)}
                ></div>
              ))}
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Home;
