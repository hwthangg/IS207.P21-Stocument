import React, { useEffect, useRef } from 'react';
import { useTheme } from '../../contexts/ThemeContext';

function LinearProgressBar({ percent, size = 300, stroke = 10 }) {
    const {theme} = useTheme()
  const canvasRef = useRef(null);
  const svgRef = useRef(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    const ctx = canvas.getContext('2d');
    const paddingLeft = 5;

    canvas.width = size + 10;
    canvas.height = stroke * 3;

    const centerY = canvas.height / 2;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Background line
    ctx.beginPath();
    ctx.moveTo(paddingLeft, centerY);
    ctx.lineTo(size, centerY);
    ctx.strokeStyle = theme == 'light'?'#D9F1E8' :'#C8C0DF';
    ctx.lineWidth = stroke;
    ctx.lineCap = 'round';

    ctx.stroke();

    // Foreground (progress) line
    ctx.beginPath();
    ctx.moveTo(paddingLeft, centerY);
    ctx.lineTo(paddingLeft + (percent / 100) * (size - paddingLeft), centerY);
    ctx.strokeStyle = theme == 'light' ?'#6DD5BE' : '#602BF8';
    ctx.lineWidth = stroke;
    ctx.lineCap = 'round';
    ctx.stroke();

    // Position SVG
    if (svgRef.current) {
      svgRef.current.style.position = 'absolute';
      svgRef.current.style.left = `${
        paddingLeft + (percent / 100) * (size - paddingLeft) - 18
      }px`; // center SVG horizontally
      svgRef.current.style.top = `${centerY - 32}px`; // fine-tune vertically
    }
  }, [percent, size, stroke, theme]);

  return (
    <div
      style={{
        position: 'relative',
        width: size,
        height: stroke + 15,
        overflow: 'visible',
      }}
    >
     <svg
  ref={svgRef}
  width="50"
  height="42"
  viewBox="0 0 53 42"
  fill="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <g filter="url(#filter0_d_316_487)">
    <rect x="5" y="6" width="33" height="19" rx="5" fill="#FDFEFE" />
    
    {/* Hiển thị phần trăm */}
    <text
      x="21.5"
      y="18"
      textAnchor="middle"
      dominantBaseline="middle"
      fontSize="12"
      
      fill="black"
    >
      {percent}%
    </text>

    {/* Mũi tên */}
    <path d="M22 28L25.4641 25H18.5359L22 28Z" fill="white" />
  </g>
  <defs>
    <filter
      id="filter0_d_316_487"
      x="0"
      y="0"
      width="53"
      height="42"
      filterUnits="userSpaceOnUse"
      colorInterpolationFilters="sRGB"
    >
      <feFlood floodOpacity="0" result="BackgroundImageFix" />
      <feColorMatrix
        in="SourceAlpha"
        type="matrix"
        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
        result="hardAlpha"
      />
      <feOffset dx="5" dy="4" />
      <feGaussianBlur stdDeviation="5" />
      <feComposite in2="hardAlpha" operator="out" />
      <feColorMatrix
        type="matrix"
        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"
      />
      <feBlend
        mode="normal"
        in2="BackgroundImageFix"
        result="effect1_dropShadow_316_487"
      />
      <feBlend
        mode="normal"
        in="SourceGraphic"
        in2="effect1_dropShadow_316_487"
        result="shape"
      />
    </filter>
  </defs>
</svg>
      <canvas ref={canvasRef} />
    </div>
  );
}

export default LinearProgressBar;
