import React, { useEffect, useRef, useState } from 'react';
import { useTheme } from '../../contexts/ThemeContext';

function CircleProgressBar({ percent, stroke = 8 }) {
  const { theme } = useTheme();
  const canvasRef = useRef(null);
  const tooltipRef = useRef(null);
  const containerRef = useRef(null);
  const [size, setSize] = useState(0);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;

    const resizeObserver = new ResizeObserver(entries => {
      for (let entry of entries) {
        // Lấy kích thước nhỏ hơn giữa width và height để đảm bảo hình tròn
        const newSize = Math.min(
          entry.contentRect.width,
          entry.contentRect.height
        );
        setSize(newSize);
      }
    });

    resizeObserver.observe(container);

    return () => {
      resizeObserver.unobserve(container);
    };
  }, []);

  useEffect(() => {
    if (!size) return;

    const canvas = canvasRef.current;
    const ctx = canvas.getContext('2d');
    if (!canvas || !ctx) return;

    // Cập nhật kích thước canvas
    canvas.width = size;
    canvas.height = size;

    const centerX = size / 2;
    const centerY = size / 2;
    const radius = (size / 2) - stroke / 2;

    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Background circle
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
    ctx.strokeStyle = theme == 'light' ? '#D9F1E8' : '#C8C0DF';
    ctx.lineWidth = stroke;
    ctx.stroke();

    // Progress arc
    const start = -Math.PI / 2;
    const end = start - (Math.PI * 2 * percent) / 100;

    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, start, end, true);
    ctx.strokeStyle = theme == 'light' ? '#6DD5BE' : '#602BF8';
    ctx.lineWidth = stroke;
    ctx.stroke();

    // Cập nhật tooltip
    if (tooltipRef.current) {
      const dotX = centerX + radius * Math.cos(end);
      const dotY = centerY + radius * Math.sin(end);
      
      const tooltipWidth = size * 0.4;
      const tooltipHeight = size * 0.3;
      const arrowSize = size * 0.05;

      tooltipRef.current.style.left = `${dotX - tooltipWidth / 2}px`;
      tooltipRef.current.style.top = `${dotY - tooltipHeight - arrowSize}px`;
      tooltipRef.current.style.width = `${tooltipWidth}px`;
      tooltipRef.current.style.height = `${tooltipHeight}px`;
      tooltipRef.current.style.fontSize = `${size * 0.12}px`;
    }
  }, [percent, stroke, theme, size]);

  return (
    <div 
      ref={containerRef} 
      style={{ 
        position: 'relative',
        width: '100%',
        height: '100%',
        minWidth: '50px', // Kích thước tối thiểu
        minHeight: '50px'
      }}
    >
      <canvas 
        ref={canvasRef} 
        style={{
          width: '100%',
          height: '100%',
          display: 'block'
        }}
      />
      
      <div 
        ref={tooltipRef}
        style={{
          position: 'absolute',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          backgroundColor: '#FDFEFE',
          borderRadius: '5px',
          boxShadow: '5px 4px 10px rgba(0, 0, 0, 0.25)',
          fontWeight: 'bold',
          color: 'black',
          pointerEvents: 'none',
          transition: 'all 0.3s ease'
        }}
      >
        {percent}%
        <div 
          style={{
            position: 'absolute',
            bottom: 0,
            left: '50%',
            transform: 'translate(-50%, 100%)',
            width: 0,
            height: 0,
            borderLeft: '5px solid transparent',
            borderRight: '5px solid transparent',
            borderTop: '5px solid #FDFEFE'
          }}
        />
      </div>
    </div>
  );
}

export default CircleProgressBar;