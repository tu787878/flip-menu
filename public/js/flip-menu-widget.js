/**
 * Flip Menu Embeddable Widget
 *
 * This script allows external websites to embed flip menus.
 *
 * Usage:
 * <div id="flip-menu-widget" data-shop-id="1" data-api-url="https://yoursite.com/wp-json" data-api-key="your-key"></div>
 * <script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
 */

(function() {
    'use strict';

    // Configuration
    var FlipMenuWidget = {
        version: '1.0.0',
        instances: [],

        /**
         * Initialize all widgets on the page
         */
        init: function() {
            var widgets = document.querySelectorAll('[data-flip-menu-widget]');

            for (var i = 0; i < widgets.length; i++) {
                this.initWidget(widgets[i]);
            }

            // Also support old method with ID
            var oldWidget = document.getElementById('flip-menu-widget');
            if (oldWidget && !oldWidget.hasAttribute('data-flip-menu-widget')) {
                this.initWidget(oldWidget);
            }
        },

        /**
         * Initialize a single widget
         */
        initWidget: function(element) {
            var shopId = element.getAttribute('data-shop-id');
            var apiUrl = element.getAttribute('data-api-url');
            var apiKey = element.getAttribute('data-api-key') || '';
            var width = element.getAttribute('data-width') || '800';
            var height = element.getAttribute('data-height') || '600';
            var theme = element.getAttribute('data-theme') || 'default';

            if (!shopId || !apiUrl) {
                console.error('Flip Menu Widget: Missing required attributes (data-shop-id and data-api-url)');
                element.innerHTML = '<p style="color: red;">Error: Missing required configuration</p>';
                return;
            }

            // Load widget
            this.loadWidget(element, {
                shopId: shopId,
                apiUrl: apiUrl,
                apiKey: apiKey,
                width: width,
                height: height,
                theme: theme
            });
        },

        /**
         * Load widget data and render
         */
        loadWidget: function(element, config) {
            var self = this;

            // Show loading state
            element.innerHTML = '<div class="flip-menu-widget-loading">Loading menu...</div>';

            // Build API URL
            var endpoint = config.apiUrl.replace(/\/$/, '') + '/flip-menu/v1/shops/' + config.shopId + '/complete';
            if (config.apiKey) {
                endpoint += '?api_key=' + encodeURIComponent(config.apiKey);
            }

            // Fetch data
            this.fetchData(endpoint, config.apiKey, function(data) {
                if (data.success && data.data) {
                    self.renderWidget(element, data.data, config);
                } else {
                    self.showError(element, {
                        type: 'api_error',
                        message: data.message || 'Failed to load menu'
                    });
                }
            }, function(error) {
                self.showError(element, error);
            });
        },

        /**
         * Fetch data from API
         */
        fetchData: function(url, apiKey, successCallback, errorCallback) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);

            if (apiKey) {
                xhr.setRequestHeader('X-API-Key', apiKey);
            }

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        successCallback(data);
                    } catch (e) {
                        errorCallback({
                            type: 'parse_error',
                            message: 'Failed to parse JSON response',
                            error: e,
                            response: xhr.responseText
                        });
                    }
                } else {
                    var errorMessage = 'HTTP ' + xhr.status;
                    var errorType = 'http_error';

                    try {
                        var errorData = JSON.parse(xhr.responseText);
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        // Response is not JSON, use status text
                        errorMessage = xhr.statusText || errorMessage;
                    }

                    if (xhr.status === 0) {
                        errorType = 'cors_error';
                        errorMessage = 'CORS error: Cannot connect to API. Check CORS settings.';
                    } else if (xhr.status === 403) {
                        errorType = 'auth_error';
                        errorMessage = 'Authentication failed. Check API key or CORS settings.';
                    } else if (xhr.status === 404) {
                        errorType = 'not_found';
                        errorMessage = 'API endpoint not found. Check shop ID.';
                    }

                    errorCallback({
                        type: errorType,
                        message: errorMessage,
                        status: xhr.status,
                        response: xhr.responseText
                    });
                }
            };

            xhr.onerror = function() {
                errorCallback({
                    type: 'network_error',
                    message: 'Network error: Cannot connect to server. Check CORS and internet connection.',
                    status: 0
                });
            };

            xhr.ontimeout = function() {
                errorCallback({
                    type: 'timeout_error',
                    message: 'Request timeout: Server took too long to respond.',
                    status: 0
                });
            };

            xhr.timeout = 30000; // 30 seconds timeout

            xhr.send();
        },

        /**
         * Render the widget
         */
        renderWidget: function(element, data, config) {
            var shop = data.shop;
            var items = data.items || [];

            if (items.length === 0) {
                element.innerHTML = '<p>No menu items available</p>';
                return;
            }

            // Create unique ID for this instance
            var uniqueId = 'flip-menu-' + Math.random().toString(36).substr(2, 9);

            // Build HTML
            var html = '';
            html += '<div class="flip-menu-widget-container" style="max-width: ' + config.width + 'px; margin: 0 auto;">';
            html += '  <div class="flip-menu-widget-header">';
            html += '    <h2 class="flip-menu-widget-title">' + this.escapeHtml(shop.name) + '</h2>';
            if (shop.description) {
                html += '    <p class="flip-menu-widget-description">' + this.escapeHtml(shop.description) + '</p>';
            }
            html += '  </div>';
            html += '  <div class="flip-menu-widget-viewer">';
            html += '    <div id="' + uniqueId + '" class="flip-menu-widget-book">';

            // Add pages
            items.forEach(function(item, index) {
                html += '      <div class="flip-menu-widget-page">';
                if (item.source_type === 'image') {
                    html += '        <img src="' + item.source_url + '" alt="' + this.escapeHtml(item.title) + '" />';
                } else {
                    html += '        <div class="flip-menu-widget-pdf-notice">';
                    html += '          <p>PDF Menu</p>';
                    html += '          <a href="' + item.source_url + '" target="_blank">View Full PDF</a>';
                    html += '        </div>';
                }
                html += '        <div class="flip-menu-widget-page-number">' + (index + 1) + '</div>';
                html += '      </div>';
            }.bind(this));

            html += '    </div>';
            html += '  </div>';
            html += '  <div class="flip-menu-widget-controls">';
            html += '    <button class="flip-menu-widget-btn flip-menu-widget-prev" data-id="' + uniqueId + '">← Previous</button>';
            html += '    <button class="flip-menu-widget-btn flip-menu-widget-next" data-id="' + uniqueId + '">Next →</button>';
            html += '  </div>';
            html += '  <div class="flip-menu-widget-footer">';
            html += '    <small>Powered by <a href="' + config.apiUrl.replace('/wp-json', '') + '" target="_blank">Flip Menu</a></small>';
            html += '  </div>';
            html += '</div>';

            element.innerHTML = html;

            // Load styles if not already loaded
            this.loadStyles();

            // Load Turn.js if available, otherwise use simple slider
            this.initializeViewer(uniqueId, config);

            // Store instance
            this.instances.push({
                id: uniqueId,
                element: element,
                config: config
            });
        },

        /**
         * Initialize the viewer (Turn.js or simple slider)
         */
        initializeViewer: function(uniqueId, config) {
            var bookElement = document.getElementById(uniqueId);

            // Check if jQuery and Turn.js are available
            if (typeof jQuery !== 'undefined' && jQuery.fn.turn) {
                this.initTurnJS(uniqueId, config);
            } else {
                // Fallback to simple slider
                this.initSimpleSlider(uniqueId);
            }

            // Setup button controls
            this.setupControls(uniqueId);
        },

        /**
         * Initialize Turn.js
         */
        initTurnJS: function(uniqueId, config) {
            var $ = jQuery;
            var $book = $('#' + uniqueId);

            $book.turn({
                width: parseInt(config.width),
                height: parseInt(config.height),
                autoCenter: true,
                display: 'double',
                acceleration: true,
                gradients: true,
                elevation: 50
            });
        },

        /**
         * Initialize simple slider (fallback)
         */
        initSimpleSlider: function(uniqueId) {
            var book = document.getElementById(uniqueId);
            var pages = book.querySelectorAll('.flip-menu-widget-page');
            var currentPage = 0;

            // Hide all pages except first
            for (var i = 1; i < pages.length; i++) {
                pages[i].style.display = 'none';
            }

            // Store slider state
            book.dataset.currentPage = '0';
            book.dataset.totalPages = pages.length;
        },

        /**
         * Setup navigation controls
         */
        setupControls: function(uniqueId) {
            var self = this;
            var prevBtn = document.querySelector('.flip-menu-widget-prev[data-id="' + uniqueId + '"]');
            var nextBtn = document.querySelector('.flip-menu-widget-next[data-id="' + uniqueId + '"]');

            if (prevBtn) {
                prevBtn.onclick = function() {
                    self.navigate(uniqueId, 'prev');
                };
            }

            if (nextBtn) {
                nextBtn.onclick = function() {
                    self.navigate(uniqueId, 'next');
                };
            }
        },

        /**
         * Navigate pages
         */
        navigate: function(uniqueId, direction) {
            var book = document.getElementById(uniqueId);

            // Check if using Turn.js
            if (typeof jQuery !== 'undefined' && jQuery.fn.turn) {
                var $ = jQuery;
                var $book = $('#' + uniqueId);
                if (direction === 'prev') {
                    $book.turn('previous');
                } else {
                    $book.turn('next');
                }
            } else {
                // Simple slider navigation
                var currentPage = parseInt(book.dataset.currentPage);
                var totalPages = parseInt(book.dataset.totalPages);
                var pages = book.querySelectorAll('.flip-menu-widget-page');

                if (direction === 'prev' && currentPage > 0) {
                    pages[currentPage].style.display = 'none';
                    currentPage--;
                    pages[currentPage].style.display = 'block';
                    book.dataset.currentPage = currentPage;
                } else if (direction === 'next' && currentPage < totalPages - 1) {
                    pages[currentPage].style.display = 'none';
                    currentPage++;
                    pages[currentPage].style.display = 'block';
                    book.dataset.currentPage = currentPage;
                }
            }
        },

        /**
         * Load widget styles
         */
        loadStyles: function() {
            if (document.getElementById('flip-menu-widget-styles')) {
                return; // Already loaded
            }

            var css = `
                .flip-menu-widget-container { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .flip-menu-widget-header { text-align: center; margin-bottom: 20px; }
                .flip-menu-widget-title { font-size: 24px; margin: 0 0 10px 0; color: #333; }
                .flip-menu-widget-description { color: #666; margin: 0; }
                .flip-menu-widget-viewer { position: relative; margin: 0 auto; }
                .flip-menu-widget-book { margin: 0 auto; background: #fff; box-shadow: 0 0 20px rgba(0,0,0,0.3); min-height: 400px; display: flex; align-items: center; justify-content: center; }
                .flip-menu-widget-page { position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: white; }
                .flip-menu-widget-page img { max-width: 100%; max-height: 100%; object-fit: contain; }
                .flip-menu-widget-page-number { position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px; }
                .flip-menu-widget-pdf-notice { text-align: center; padding: 20px; }
                .flip-menu-widget-controls { text-align: center; margin: 20px 0; }
                .flip-menu-widget-btn { background: #0073aa; color: white; border: none; padding: 10px 20px; margin: 0 5px; cursor: pointer; font-size: 14px; border-radius: 4px; }
                .flip-menu-widget-btn:hover { background: #005177; }
                .flip-menu-widget-footer { text-align: center; margin-top: 10px; color: #999; font-size: 12px; }
                .flip-menu-widget-footer a { color: #0073aa; text-decoration: none; }
                .flip-menu-widget-loading { text-align: center; padding: 40px; color: #666; }
            `;

            var style = document.createElement('style');
            style.id = 'flip-menu-widget-styles';
            style.textContent = css;
            document.head.appendChild(style);
        },

        /**
         * Show error message with debugging info
         */
        showError: function(element, error) {
            var errorHtml = '<div class="flip-menu-widget-error" style="padding: 20px; background: #fee; border: 2px solid #c00; border-radius: 5px;">';
            errorHtml += '<h3 style="color: #c00; margin-top: 0;">⚠️ Flip Menu Error</h3>';
            errorHtml += '<p><strong>Error Type:</strong> ' + this.escapeHtml(error.type || 'unknown') + '</p>';
            errorHtml += '<p><strong>Message:</strong> ' + this.escapeHtml(error.message || 'Unknown error') + '</p>';

            // Add helpful troubleshooting based on error type
            if (error.type === 'cors_error' || error.type === 'network_error') {
                errorHtml += '<div style="background: #fff; padding: 10px; margin: 10px 0; border-left: 3px solid #c00;">';
                errorHtml += '<p><strong>Troubleshooting CORS/Network Errors:</strong></p>';
                errorHtml += '<ol style="margin: 5px 0; padding-left: 20px;">';
                errorHtml += '<li>Go to WordPress Admin → Flip Menu → API & Embed</li>';
                errorHtml += '<li>Check "Enable API" checkbox</li>';
                errorHtml += '<li>Check "Enable CORS" checkbox</li>';
                errorHtml += '<li>Set "Allowed Origins" to <code>*</code> or your domain</li>';
                errorHtml += '<li>Save settings and refresh this page</li>';
                errorHtml += '</ol>';
                errorHtml += '</div>';
            } else if (error.type === 'auth_error') {
                errorHtml += '<div style="background: #fff; padding: 10px; margin: 10px 0; border-left: 3px solid #c00;">';
                errorHtml += '<p><strong>Authentication Issue:</strong></p>';
                errorHtml += '<ul style="margin: 5px 0; padding-left: 20px;">';
                errorHtml += '<li>Check if API key is correct</li>';
                errorHtml += '<li>Verify API is enabled in WordPress admin</li>';
                errorHtml += '<li>Check CORS allowed origins</li>';
                errorHtml += '</ul>';
                errorHtml += '</div>';
            } else if (error.type === 'not_found') {
                errorHtml += '<div style="background: #fff; padding: 10px; margin: 10px 0; border-left: 3px solid #c00;">';
                errorHtml += '<p><strong>Not Found:</strong></p>';
                errorHtml += '<ul style="margin: 5px 0; padding-left: 20px;">';
                errorHtml += '<li>Check if shop ID exists</li>';
                errorHtml += '<li>Verify API URL is correct</li>';
                errorHtml += '<li>Check WordPress permalinks are enabled</li>';
                errorHtml += '</ul>';
                errorHtml += '</div>';
            }

            // Show technical details in console
            errorHtml += '<details style="margin-top: 10px;">';
            errorHtml += '<summary style="cursor: pointer; color: #666;">Show Technical Details</summary>';
            errorHtml += '<pre style="background: #f5f5f5; padding: 10px; overflow: auto; font-size: 11px;">';
            errorHtml += JSON.stringify(error, null, 2);
            errorHtml += '</pre>';
            errorHtml += '</details>';

            errorHtml += '</div>';

            element.innerHTML = errorHtml;

            // Log to console for debugging
            console.error('Flip Menu Widget Error:', error);
            console.log('To fix this error, check the troubleshooting steps above.');
        },

        /**
         * Escape HTML to prevent XSS
         */
        escapeHtml: function(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.textContent = String(text);
            return div.innerHTML;
        }
    };

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            FlipMenuWidget.init();
        });
    } else {
        FlipMenuWidget.init();
    }

    // Expose to window for manual initialization
    window.FlipMenuWidget = FlipMenuWidget;

})();
