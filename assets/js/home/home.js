import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { HomePage } from './../../js/components/HomePage/HomePage'

document.addEventListener('DOMContentLoaded', function () {
    console.log("=> home.js loaded");
    const homePage = document.getElementById("homepage-root");
    if (homePage) {
        createRoot(homePage).render(<HomePage />);
    }
});