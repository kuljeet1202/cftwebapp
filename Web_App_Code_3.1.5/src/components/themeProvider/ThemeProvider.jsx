"use client"
import React, { useEffect } from 'react';

const ThemeProvider = ({ children }) => {
  useEffect(() => {
    const changeColors = () => {
      // Replace process.env with hardcoded values for illustration purposes
      // Replace these with your actual environment variable usage
      const loaderColor = process.env.NEXT_PUBLIC_COLOR; // process.env.NEXT_PUBLIC_COLOR
      const secondaryColor = process.env.NEXT_PUBLIC_SECONDARY_COLOR; // process.env.NEXT_PUBLIC_SECONDARY_COLOR

      document.documentElement.style.setProperty('--loader-color', loaderColor)
      document.documentElement.style.setProperty('--secondary-color', secondaryColor)
    };

    changeColors(); // Call the function when the component mounts or updates
  }, []); // Empty dependency array ensures it only runs once when the component mounts

  return <div>{children}</div>;
};

export default ThemeProvider;

