import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

import UserCard from './../../js/components/Card/UserCard';

document.addEventListener('DOMContentLoaded', () => {
    console.log('=> User.js loaded!');

    
    const lastDeveloperRoot = document.getElementById('last-developer-profile');
    if (lastDeveloperRoot) {
        const root = createRoot(lastDeveloperRoot);
        root.render(<UserCard user={window.__INITIAL_DATA__.lastDeveloper} />);
    }

    const lastClientRoot = document.getElementById('last-client-profile');
    if (lastClientRoot) {
        const root = createRoot(lastClientRoot);
        root.render(<UserCard user={window.__INITIAL_DATA__.lastClient} />);
    }

});