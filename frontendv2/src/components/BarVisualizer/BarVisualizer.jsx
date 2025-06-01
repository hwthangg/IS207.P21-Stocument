import React, { useEffect, useState } from 'react';
import  styles from './BarVisualizer.module.css';
import { useTheme } from '../../contexts/ThemeContext';

const BarVisualizer = () => {
  const {theme} = useTheme()
  const [bars, setBars] = useState([1,2,3,4,5,6,7,8]);

  useEffect(() => {
  ; // cập nhật mỗi 300ms


  }, [bars]);

  return (
    <div className={styles.bar_container}>
      {bars.map((height, i) => (
        <div className={styles.bar_wrapper} key={i}>
          <div className={styles.bar_bg} style={theme == 'light' ? {backgroundColor:'#D9F1E8'}:{backgroundColor:'#C8C0DF'}}/>
          <div className={styles.bar_fill} style={{ height: `${height * 10}%`, backgroundColor: theme == 'light' ? '#6DD5BE' :'#602BF8'  }} />
        </div>
      ))}
    </div>
  );
};

export default BarVisualizer;
