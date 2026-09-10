/**
 * Curved Animated Sidebar JS (Ultra-Polished Left Layout)
 * Converted from React + Framer Motion (sidebar.tsx)
 * Features:
 * - Dynamic Full-Height SVG Bezier Morphing Curve (Duration: 0.85s, Ease: [0.76, 0, 0.24, 1])
 * - Staggered Kinetic Word/Letter Spans with Preserved Spacing
 * - Backdrop Blur Overlay with ESC key support
 */
(function (window, document) {
    'use strict';

    // Cubic Bezier Easing Helper matching Framer Motion [0.76, 0, 0.24, 1]
    function cubicBezier(t) {
        return (1 - t) * (1 - t) * (1 - t) * 0 +
               3 * (1 - t) * (1 - t) * t * 0.76 +
               3 * (1 - t) * t * t * 0.24 +
               t * t * t * 1;
    }

    class CurvedSidebar {
        constructor(options = {}) {
            this.toggleBtnId = options.toggleBtnId || 'curvedSidebarToggle';
            this.panelId = options.panelId || 'curvedSidebarPanel';
            this.backdropId = options.backdropId || 'curvedSidebarBackdrop';
            this.pathId = options.pathId || 'curvedSidebarPath';
            this.svgId = options.svgId || 'curvedSidebarSvg';
            
            this.isDesktop = window.innerWidth >= 1024;
            this.isOpen = typeof options.defaultOpen !== 'undefined' ? options.defaultOpen : true; // Open by default
            this.animFrameId = null;
            this.animDuration = 750; // ms

            this.init();
        }

        init() {
            this.toggleBtn = document.getElementById(this.toggleBtnId);
            this.panel = document.getElementById(this.panelId);
            this.backdrop = document.getElementById(this.backdropId);
            this.path = document.getElementById(this.pathId);
            this.svg = document.getElementById(this.svgId);

            if (!this.panel || !this.toggleBtn) {
                return;
            }

            // Set initial open/collapsed state (Default open on desktop)
            if (this.isOpen) {
                this.panel.classList.add('is-active');
                this.toggleBtn.classList.add('is-active');
                this.toggleBtn.setAttribute('aria-expanded', 'true');
            } else {
                this.panel.classList.remove('is-active');
                this.toggleBtn.classList.remove('is-active');
                this.toggleBtn.setAttribute('aria-expanded', 'false');
            }

            // Bind events
            this.toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });

            if (this.backdrop) {
                this.backdrop.addEventListener('click', () => this.close());
            }

            // Bind explicit close buttons inside panel
            const closeBtns = this.panel.querySelectorAll('.curved-sidebar-close-btn, #curvedSidebarCloseBtn');
            closeBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.close();
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                }
            });

            window.addEventListener('resize', () => {
                this.updateSvgDimensions();
                if (this.isOpen) {
                    this.setPath(0);
                } else {
                    this.setPath(70);
                }
            });

            // Prepare letter spans for staggered kinetic wave
            this.initLetterSplit();

            // Initial SVG path state
            this.updateSvgDimensions();
            this.setPath(this.isOpen ? 0 : 70);
        }

        updateSvgDimensions() {
            if (!this.svg) return;
            const h = window.innerHeight || document.documentElement.clientHeight || 900;
            this.svg.setAttribute('viewBox', `0 0 70 ${h}`);
        }

        initLetterSplit() {
            const headings = this.panel.querySelectorAll('.curved-nav-heading');
            headings.forEach(heading => {
                if (heading.dataset.split) return;
                heading.dataset.split = 'true';
                
                const rawText = heading.textContent.trim();
                const words = rawText.split(/\s+/);
                heading.innerHTML = '';

                let charCount = 0;
                words.forEach((word) => {
                    const wordSpan = document.createElement('span');
                    wordSpan.className = 'curved-nav-word';

                    word.split('').forEach((char) => {
                        const charSpan = document.createElement('span');
                        charSpan.className = 'curved-nav-letter';
                        charSpan.textContent = char;
                        charSpan.style.transitionDelay = `${charCount * 18}ms`;
                        wordSpan.appendChild(charSpan);
                        charCount++;
                    });

                    heading.appendChild(wordSpan);
                });
            });

            // Handle link click & smooth scrolling
            const links = this.panel.querySelectorAll('.curved-nav-item');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    const href = link.getAttribute('href');
                    if (href && !href.startsWith('javascript:')) {
                        try {
                            const targetUrl = new URL(href, window.location.href);
                            // If it's a hash link on the current page
                            if (targetUrl.pathname === window.location.pathname && targetUrl.hash) {
                                e.preventDefault();
                                const hashKey = targetUrl.hash.replace(/^#/, '');
                                if (typeof window.scrollToSection === 'function') {
                                    window.scrollToSection(hashKey);
                                } else {
                                    const el = document.getElementById(hashKey) || document.getElementById('section-about') || document.getElementById('info_ruangan');
                                    if (el) {
                                        el.scrollIntoView({ behavior: 'smooth' });
                                    }
                                }
                                if (history.pushState) {
                                    history.pushState(null, '', targetUrl.hash);
                                }
                            }
                        } catch (err) {
                            // Fallback standard navigation
                        }
                    }
                    // Auto-close on mobile screen
                    if (window.innerWidth < 1024) {
                        setTimeout(() => this.close(), 180);
                    }
                });
            });
        }

        setPath(controlX) {
            if (!this.path) return;
            const h = window.innerHeight || document.documentElement.clientHeight || 900;
            const d = `M0 0 L0 ${h} Q${controlX} ${h / 2} 0 0`;
            this.path.setAttribute('d', d);
        }

        animateSvgCurve(fromX, toX, duration, onComplete) {
            if (this.animFrameId) {
                cancelAnimationFrame(this.animFrameId);
            }

            this.updateSvgDimensions();
            const startTime = performance.now();

            const step = (now) => {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = cubicBezier(progress);
                const currentX = fromX + (toX - fromX) * eased;

                this.setPath(currentX);

                if (progress < 1) {
                    this.animFrameId = requestAnimationFrame(step);
                } else {
                    this.animFrameId = null;
                    if (typeof onComplete === 'function') onComplete();
                }
            };

            this.animFrameId = requestAnimationFrame(step);
        }

        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        }

        open() {
            if (this.isOpen) return;
            this.isOpen = true;

            // Update DOM classes
            this.toggleBtn.classList.add('is-active');
            this.toggleBtn.setAttribute('aria-expanded', 'true');
            if (this.backdrop && window.innerWidth < 1024) {
                this.backdrop.classList.add('is-active');
            }
            this.panel.classList.add('is-active');

            // Morph curve from bulging (70) to flat (0)
            this.animateSvgCurve(70, 0, 800);
        }

        close() {
            if (!this.isOpen) return;
            this.isOpen = false;

            // Update DOM classes
            this.toggleBtn.classList.remove('is-active');
            this.toggleBtn.setAttribute('aria-expanded', 'false');
            if (this.backdrop) this.backdrop.classList.remove('is-active');
            this.panel.classList.remove('is-active');

            // Morph curve from flat (0) back to bulging (70)
            this.animateSvgCurve(0, 70, 700);
        }
    }

    // Export globally & auto-init if elements exist
    window.CurvedSidebar = CurvedSidebar;

    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('curvedSidebarPanel')) {
            window.curvedSidebarInstance = new CurvedSidebar();
        }
    });

})(window, document);
