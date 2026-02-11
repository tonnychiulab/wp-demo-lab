/**
 * WP Demo Lab: Icons Gallery Script
 * Handles pagination and grid rendering for Dashicons.
 */

// List of standard Dashicons (subset for demo purposes, can be expanded)
const dashicons = [
    'dashicons-menu', 'dashicons-admin-site', 'dashicons-dashboard', 'dashicons-admin-post',
    'dashicons-admin-media', 'dashicons-admin-links', 'dashicons-admin-page', 'dashicons-admin-comments',
    'dashicons-admin-appearance', 'dashicons-admin-plugins', 'dashicons-admin-users', 'dashicons-admin-tools',
    'dashicons-admin-settings', 'dashicons-admin-network', 'dashicons-admin-home', 'dashicons-admin-generic',
    'dashicons-admin-collapse', 'dashicons-filter', 'dashicons-admin-customizer', 'dashicons-admin-multisite',
    'dashicons-welcome-write-blog', 'dashicons-welcome-add-page', 'dashicons-welcome-view-site', 'dashicons-welcome-widgets-menus',
    'dashicons-welcome-comments', 'dashicons-welcome-learn-more', 'dashicons-format-aside', 'dashicons-format-image',
    'dashicons-format-gallery', 'dashicons-format-video', 'dashicons-format-status', 'dashicons-format-quote',
    'dashicons-format-chat', 'dashicons-format-audio', 'dashicons-camera', 'dashicons-images-alt',
    'dashicons-images-alt2', 'dashicons-video-alt', 'dashicons-video-alt2', 'dashicons-video-alt3',
    'dashicons-media-archive', 'dashicons-media-audio', 'dashicons-media-code', 'dashicons-media-default',
    'dashicons-media-document', 'dashicons-media-interactive', 'dashicons-media-spreadsheet', 'dashicons-media-text',
    'dashicons-media-video', 'dashicons-playlist-audio', 'dashicons-playlist-video', 'dashicons-controls-play',
    'dashicons-controls-pause', 'dashicons-controls-forward', 'dashicons-controls-skipforward', 'dashicons-controls-back',
    'dashicons-controls-skipback', 'dashicons-controls-repeat', 'dashicons-controls-volumeon', 'dashicons-controls-volumeoff',
    'dashicons-sme', 'dashicons-id', 'dashicons-id-alt', 'dashicons-products',
    'dashicons-awards', 'dashicons-forms', 'dashicons-testimonial', 'dashicons-portfolio',
    'dashicons-book', 'dashicons-book-alt', 'dashicons-download', 'dashicons-upload',
    'dashicons-backup', 'dashicons-clock', 'dashicons-lightbulb', 'dashicons-microphone',
    'dashicons-desktop', 'dashicons-tablet', 'dashicons-smartphone', 'dashicons-phone',
    'dashicons-smiley', 'dashicons-index-card', 'dashicons-carrot', 'dashicons-building',
    'dashicons-store', 'dashicons-album', 'dashicons-palmtree', 'dashicons-tickets-alt',
    'dashicons-money', 'dashicons-thumbs-up', 'dashicons-thumbs-down', 'dashicons-layout'
];

document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('wpdl-icons-grid');
    const prevBtn = document.getElementById('wpdl-prev');
    const nextBtn = document.getElementById('wpdl-next');
    const pageInfo = document.getElementById('wpdl-page-info');

    if (!grid) return; // Not on our page

    const perPage = 24;
    let currentPage = 1;
    const totalPages = Math.ceil(dashicons.length / perPage);

    function renderIcons(page) {
        grid.innerHTML = '';

        const start = (page - 1) * perPage;
        const end = start + perPage;
        const icons = dashicons.slice(start, end);

        icons.forEach(iconClass => {
            const div = document.createElement('div');
            div.className = 'wpdl-icon-item';
            div.setAttribute('data-class', iconClass);

            // Create icon element
            const span = document.createElement('span');
            span.className = 'dashicons ' + iconClass;
            div.appendChild(span);

            // Copy on click
            div.addEventListener('click', function () {
                const html = `<span class="dashicons ${iconClass}"></span>`;
                navigator.clipboard.writeText(html).then(() => {
                    alert('Copied: ' + html);
                });
            });

            grid.appendChild(div);
        });

        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    prevBtn.addEventListener('click', function () {
        if (currentPage > 1) {
            currentPage--;
            renderIcons(currentPage);
        }
    });

    nextBtn.addEventListener('click', function () {
        if (currentPage < totalPages) {
            currentPage++;
            renderIcons(currentPage);
        }
    });

    // Initial render
    renderIcons(currentPage);
});
