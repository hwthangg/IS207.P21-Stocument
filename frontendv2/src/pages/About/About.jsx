import React, { useState, useEffect } from 'react';
import styles from './About.module.css';
import { useTheme } from "../../contexts/ThemeContext.jsx";
import { Link } from 'react-router-dom';
import { useHeader } from '../../contexts/HeaderContext.jsx';
const About = () => {
  const {theme} = useTheme()
  const [currentSlide, setCurrentSlide] = useState(0);
  const {setActiveLink} = useHeader()


  // Carousel data
  const carouselItems = [
    { img: "../../assets/anh9.jpg", title: "Kinh tế học đại cương" },
    { img: "../../assets/anh10.jpg", title: "Giải tích" },
    { img: "../../assets/anh11.png", title: "Cấu trúc dữ liệu & giải thuật" },
    { img: "../../assets/anh15.webp", title: "OOP" },
    { img: "../../assets/anh14.jpg", title: "Kinh tế chính trị" },
    { img: "../../assets/anh16.jpg", title: "Đại số tuyến tính" }
  ];

  // Feedback data
  const feedbacks = [
    {
      avatar: new URL("../../assets/avatar1.jpg", import.meta.url).href,
      name: "Nguyễn Quốc Nhật Khang",
      role: "Sinh viên năm 2 của trường Đại Học Công Nghệ Thông tin",
      comment: "Từ khi biết đến Stocument, việc học của mình trở nên nhẹ nhàng hơn rất nhiều..."
    },
    {
      avatar: new URL("../../assets/avatar2.jpg", import.meta.url).href,
      name: "Nguyễn Thị Thanh Hằng",
      role: "Sinh viên năm 3 của trường Đại Học Công Nghệ Thông tin",
      comment: "Stocument đã trở thành công cụ không thể thiếu đối với mình trong việc học và chia sẻ tài liệu..."
    }
  ];

  // Stats data
  const stats = [
    { number: "29+", label: "Môn học khác nhau" },
    { number: "1.500+", label: "Tài liệu đã được tải lên" },
    { number: "8.000+", label: "Số lượt truy cập hàng tháng" },
    { number: "30.000+", label: "Thành viên tham gia" }
  ];

  // Carousel functionality
  const nextSlide = () => {
    setCurrentSlide((prev) => 
      prev < carouselItems.length - getItemsPerPage() ? prev + 1 : prev
    );
  };

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev > 0 ? prev - 1 : prev));
  };

  const goToSlide = (index) => {
    setCurrentSlide(index);
  };

  const getItemsPerPage = () => {
    return window.innerWidth <= 768 ? 1 : 3;
  };
useEffect(()=>{
    setActiveLink("Giới thiệu")
    window.scrollTo(0, 0);
}, [])
  useEffect(() => {

    const handleResize = () => {
      if (currentSlide > carouselItems.length - getItemsPerPage()) {
        setCurrentSlide(carouselItems.length - getItemsPerPage());
      }
    };

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, [currentSlide, carouselItems.length]);

  useEffect(() => {
   
  }, [theme]);
  const [general, setGeneral] = useState({});
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
  
     
      fetchGeneral();

      setActiveLink("Giới thiệu");
    }, [setActiveLink]);

  return (
    <div
      className={`${styles.layout} ${theme == "dark" ? styles.darkTheme : ""}`}
    >
      {/* Hero Section */}
      <section className={styles.introSection}>
        <img
          className={styles.backgroundImg}
          src={new URL("../../assets/banner.jpg", import.meta.url).href}
          alt="Background"
        />

        <div className={styles.introContent}>
          <div className={styles.introCard}>
            <h2 className={styles.introTitle}>Sharing is Caring</h2>
            <p className={styles.introText}>
              {general.site_about}
            </p>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className={styles.statsGrid}>
        {stats.map((stat, index) => (
          <div key={index} className={styles.statItem}>
            <div className={styles.statNumber}>{stat.number}</div>
            <p className={styles.statLabel}>{stat.label}</p>
          </div>
        ))}
      </section>

      {/* Main Content Section */}
      <section className={styles.contentbox}>
        <div className={styles.intro}>
          <p className={styles.headingPrimary}>Tri thức là sức mạnh</p>
          <p className={styles.headingSecondary}>
            Nơi sở hữu tài liệu giúp bạn vượt qua mọi kì thi
          </p>
        </div>

        {/* Carousel */}
        <div className={styles.carouselContainer}>
          <button
            className={`${styles.nav} ${styles.prev}`}
            onClick={prevSlide}
          >
            <p>&#10094;</p>
          </button>

          <div
            className={styles.carouselTrack}
            style={{
              transform: `translateX(-${
                currentSlide * (100 / getItemsPerPage())
              }%)`
            }}
          >
            {carouselItems.map((item, index) => (
              <div key={index} className={styles.imageCard}>
                <img
                  className={styles.carouselImage}
                  src={new URL(item.img, import.meta.url).href}
                  alt={item.title}
                />
                <p className={styles.imageTitle}>{item.title}</p>
              </div>
            ))}
          </div>

          <button
            className={`${styles.nav} ${styles.next}`}
            onClick={nextSlide}
          >
             <p>&#10095;</p>
          </button>

          <div className={styles.dots}>
            {Array.from({
              length: carouselItems.length - getItemsPerPage() + 1,
            }).map((_, index) => (
              <button
                key={index}
                className={`${styles.dot} ${
                  index === currentSlide ? styles.active : ""
                }`}
                onClick={() => goToSlide(index)}
              />
            ))}
          </div>
        </div>

        {/* Feedback Section */}
        <p className={styles.feedbackHeading}>Phản hồi từ người dùng</p>

        <div className={styles.feedbackContainer}>
          {feedbacks.map((feedback, index) => (
            <div key={index} className={styles.feedbackCard}>
              <img
                className={styles.avatar}
                src={feedback.avatar}
                alt="Avatar"
              />
              <div className={styles.feedbackText}>
                <a className={styles.link} href="#">
                  <p className={styles.userName}>{feedback.name}</p>
                </a>
                <p className={styles.userRole}>{feedback.role}</p>
                <p className={styles.userComment}>{feedback.comment}</p>
              </div>
            </div>
          ))}
        </div>

        {/* CTA Section */}
        <Link to={'/document'}>
          <div className={styles.ctaContainer}>
            <p className={styles.ctaText}>Tìm kiếm tài liệu ngay</p>
          </div>
        </Link>
      </section>
    </div>
  );
};

export default About;