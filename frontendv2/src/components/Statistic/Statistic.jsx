import React, { useEffect, useRef, useState } from 'react';
import styles from '../Statistic/Statistic.module.css';
import LinearProgressBar from '../LinerProgressBar/LinearProgrssBar';
import CircleProgressBar from '../CircleProgressBar/CircleProgressBar';
import ResponseRateChart from '../LineChart/LineChart';


import { useTheme } from '../../contexts/ThemeContext';
import BarVisualizer from '../BarVisualizer/BarVisualizer';

function Statistic() {
  const {theme} = useTheme()
  const cardRefs = [useRef(null), useRef(null), useRef(null)];
  const [linearProgressWidth, setLinearProgressWidth] = useState();

  const myData = {
    labels: ["1/6", "2/6", "3/6", "4/6", "5/6", "6/6", "7/6", "8/6"],
    values: [40, 35, 60, 55, 80, 75, 90, 185],
  };

  useEffect(() => {
    if (cardRefs[0].current) {
      setLinearProgressWidth(cardRefs[0].current.offsetWidth);
    }
  }, []);

  return (
    <div className={styles.statisticContainer} style={{color: `${theme == 'light' ? 'black' : 'white'}`}}>
      <div className={styles.statisticGrid}>
        {/* Card 1 */}
        <div
          className={styles.statisticCard}
          ref={cardRefs[0]}
          style={{
            boxShadow:
              theme == "light"
                ? "6px 6px 30px rgba(125, 153, 10, 0.2),  2px 2px 4px rgba(86, 144, 198, 0.11)"
                : "6px 6px 30px rgba(125, 153, 180, 0.2),  2px 2px 4px rgba(86, 144, 198, 0.11)",
          }}
        >
          <div className={styles.cardHeader} >
            <p className={styles.cardTitle}>Lượt phản hồi mới</p>
            <p className={styles.cardValue}>85</p>
          </div>
          <div className={styles.linearProgressWrapper} style={{borderTop: `1px solid ${theme == 'light' ? '#DDDBE2' : '#464756'}`}}>
            <LinearProgressBar
              percent={75}
              size={linearProgressWidth * 0.6}
              stroke={8}
            />
            <p className={styles.progressLabel}>Tỷ lệ phản hồi</p>
          </div>
        </div>

        {/* Card 2 */}
        <div
          className={styles.statisticCard}
          ref={cardRefs[1]}
          style={{
            display: "flex",
            flexDirection: "row",
            justifyContent: "center",
            alignItems: "center",
            boxShadow:
              theme === "dark"
                ? "6px 6px 30px rgba(125, 153, 180, 0.2), 2px 2px 4px rgba(86, 144, 198, 0.11)"
                : "6px 6px 30px rgba(125, 153, 10, 0.2), 2px 2px 4px rgba(86, 144, 198, 0.11)",
          }}
        >
          <div
            className={styles.cardHeader}
            style={{
              padding: "1em 0 1em 1em",
              width: "100%",
              height: "100%",
              boxSizing: "border-box",
            }}
          >
            <p className={styles.cardTitle}>Lượt tải về hôm nay</p>
            <p className={styles.cardValue}>21</p>
            <div className={styles.cardBody}>
              <div className={styles.statGrid}>
                <p>
                  <b>60%</b>
                </p>
                <p>Mục tiêu hàng tuần</p>
                <p>
                  <b>72</b>
                </p>
                <p>Tuần này</p>
              </div>
            </div>
          </div>

          <div className={styles.circleProgressWrapper} >
            <CircleProgressBar percent={15} stroke={10} />
          </div>
        </div>

        {/* Card 3 */}
        <div
          className={styles.statisticCard}
          style={{
            flexDirection: "row",
            justifyContent: "center",
            alignItems: "center",
            position: 'relative',
            boxShadow:
              theme === "dark"
                ? "6px 6px 30px rgba(125, 153, 180, 0.2), 2px 2px 4px rgba(86, 144, 198, 0.11)"
                : "6px 6px 30px rgba(125, 153, 10, 0.2), 2px 2px 4px rgba(86, 144, 198, 0.11)",
          }}
        >
          <div
            className={styles.cardHeader}
            style={{
              padding: "1em",
              width: "100%",
              height: "100%",
              boxSizing: "border-box",
            }}
          >
            <div style={{ display: "flex", flexDirection: "row", flex: 1}}>
              <div
                style={{
                  display: "flex",
                  flexDirection: "column",
                  justifyContent: "space-around",
                }}
              >
                <p className={styles.cardTitle} style={{ paddingRight: "1em" }}>
                  Lượt truy cập đồng thời cao nhất
                </p>
                <p className={styles.cardValue}>98</p>
                <p className={styles.cardAccessDes}>
                  Người dùng truy cập cùng lúc
                </p>
              </div>
              <div
                style={{
                  flex: 1,
                  display: "flex",
                  flexDirection: "column",
                  justifyContent: 'flex-end',
                  alignItems:'flex-end',
                  
                }}
              >
              <BarVisualizer/>
              <div style={{border: '1px solid', position: 'absolute', width: '100%',bottom: '7%', right:0 ,  borderColor: '#DDDBE2'}}></div>
              
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Biểu đồ LineChart */}
      <p className={styles.cardTitle}>Lượt truy cập</p>
      <br />
      <ResponseRateChart data={myData} />
    </div>
  );
}

export default Statistic;
