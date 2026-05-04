/**
 * MathJax Utilities - Quản lý render công thức toán học cho nội dung động
 * Version: 1.0.0
 */

(function() {
    'use strict';

    // Cấu hình MathJax
    window.MathJax = {
        tex: {
            inlineMath: [
                ['$', '$'],
                ['\\(', '\\)']
            ],
            displayMath: [
                ['$$', '$$'],
                ['\\[', '\\]']
            ],
            processEscapes: true,
            processEnvironments: true
        },
        options: {
            skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre']
        },
        startup: {
            pageReady: () => {
                console.log('MathJax đã sẵn sàng!');
                // Tự động render cho nội dung ban đầu
                MathJaxUtils.renderMathJaxForDynamicContent();
            }
        }
    };

    // MathJax Utilities Object
    window.MathJaxUtils = {
        /**
         * Render MathJax cho nội dung động
         * @param {HTMLElement} container - Container chứa nội dung cần render
         */
        renderMathJaxForDynamicContent: function(container = null) {
            if (window.MathJax && window.MathJax.typesetPromise) {
                const targetContainer = container || document;
                
                // Đợi một chút để đảm bảo DOM đã được cập nhật
                setTimeout(() => {
                    window.MathJax.typesetPromise([targetContainer])
                        .then(() => {
                            console.log('MathJax render thành công cho nội dung động!');
                        })
                        .catch((err) => {
                            console.error('MathJax render lỗi:', err);
                        });
                }, 100);
            } else {
                console.warn('MathJax chưa được load hoặc không khả dụng');
            }
        },

        /**
         * Render MathJax cho một element cụ thể
         * @param {HTMLElement} element - Element cần render
         */
        renderMathJaxForElement: function(element) {
            if (window.MathJax && window.MathJax.typesetPromise && element) {
                window.MathJax.typesetPromise([element])
                    .then(() => {
                        console.log('MathJax render thành công cho element!');
                    })
                    .catch((err) => {
                        console.error('MathJax render lỗi:', err);
                    });
            }
        },

        /**
         * Kiểm tra xem nội dung có chứa công thức toán học không
         * @param {string} content - Nội dung cần kiểm tra
         * @returns {boolean}
         */
        hasMathContent: function(content) {
            if (!content) return false;
            
            const mathPatterns = [
                /\$[^$]+\$/,           // $...$
                /\$\$[^$]+\$\$/,       // $$...$$
                /\\\([^)]+\\\)/,       // \(...\)
                /\\\[[^\]]+\\\]/       // \[...\]
            ];
            
            return mathPatterns.some(pattern => pattern.test(content));
        },

        /**
         * Thiết lập observer để tự động render MathJax khi có nội dung mới
         */
        setupMathJaxObserver: function() {
            if (window.MathJax) {
                // Tạo MutationObserver để theo dõi thay đổi DOM
                const observer = new MutationObserver((mutations) => {
                    let shouldRender = false;
                    
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            // Kiểm tra xem có nội dung toán học mới được thêm không
                            mutation.addedNodes.forEach((node) => {
                                if (node.nodeType === Node.ELEMENT_NODE) {
                                    if (this.hasMathContent(node.textContent)) {
                                        shouldRender = true;
                                    }
                                }
                            });
                        }
                    });
                    
                    if (shouldRender) {
                        this.renderMathJaxForDynamicContent();
                    }
                });
                
                // Bắt đầu quan sát
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
                
                console.log('MathJax Observer đã được thiết lập');
                return observer;
            }
        },

        /**
         * Load MathJax từ CDN
         * @param {string} cdnUrl - URL của MathJax CDN
         * @returns {Promise}
         */
        loadMathJax: function(cdnUrl = 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js') {
            return new Promise((resolve, reject) => {
                if (window.MathJax) {
                    resolve(window.MathJax);
                    return;
                }

                const script = document.createElement('script');
                script.src = cdnUrl;
                script.async = true;
                script.onload = () => {
                    console.log('MathJax đã được load thành công!');
                    resolve(window.MathJax);
                };
                script.onerror = () => {
                    console.error('Không thể load MathJax từ CDN');
                    reject(new Error('MathJax load failed'));
                };
                document.head.appendChild(script);
            });
        },

        /**
         * Khởi tạo MathJax cho trang
         */
        init: function() {
            // Load MathJax nếu chưa có
            this.loadMathJax().then(() => {
                // Thiết lập observer
                this.setupMathJaxObserver();
                
                // Render MathJax cho nội dung ban đầu
                this.renderMathJaxForDynamicContent();
            }).catch((error) => {
                console.error('Lỗi khởi tạo MathJax:', error);
            });
        }
    };

    // Tự động khởi tạo khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            MathJaxUtils.init();
        });
    } else {
        MathJaxUtils.init();
    }

})(); 