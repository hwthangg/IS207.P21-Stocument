import React from 'react';
import { Line } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip
} from 'chart.js';
import { useTheme } from '../../contexts/ThemeContext';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip
);

const ResponseRateChart = ({ data }) => {
  // Lọc ngày chẵn
    const {theme} = useTheme()

  const chartData = {
    labels: data.labels,
    datasets: [{
      label: 'Lượt truy cập',
      data: data.values,
      borderColor: theme == 'light' ?'#6DD5BE' : '#602BF8',
      backgroundColor: 'rgba(52, 152, 219, 0.1)',
      tension: 0.4,
      fill: true,
      borderWidth: 3,
      
      
    }]
  };

  const gridColor = theme === 'light' ? '#F0F0F0' : '#9e9e9e';

const options = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    x: {
      ticks: {
        autoSkip: false,
        maxRotation: 0,
        minRotation: 0
      },
      grid: {
        display: true,
        color: gridColor // thêm dòng này
      }
    },
    y: {
      beginAtZero: true,
      ticks: {
        stepSize: 25
      },
      grid: {
        color: gridColor // thêm dòng này
      }
    }
  },
  plugins: {
    legend: {
      display: false,
    },
    tooltip: {
      mode: 'index',
      intersect: false
    }
  }
};


  return (
    <div style={{ 
     
      height: '200px',
      position: 'relative'
    }}>
      <div style={{ 
        width: '100%',
        height: 'calc(100% - 30px)' // Trừ đi chiều cao của tiêu đề
      }}>
        <Line 
          data={chartData} 
          options={options} 
        
          redraw // Đảm bảo vẽ lại khi resize
        />
      </div>
    </div>
  );
};

ResponseRateChart.defaultProps = {
  data: {
    labels: ['18/5', '19/5', '20/5', '21/5', '22/5', '23/5', '24/5', '25/5', '26/5', '27/5'],
    values: [25, 30, 50, 45, 75, 70, 100, 90, 125, 110]
  }
};

export default ResponseRateChart;