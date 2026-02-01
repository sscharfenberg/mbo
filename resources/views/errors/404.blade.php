<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>404: Page not found</title>
        <meta name="color-scheme" content="light dark">
        <link rel="icon" type="image/png" href="/favicons/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicons/favicon.svg" />
        <link rel="shortcut icon" href="/favicons/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="Audio.Catalogue" />
        <link rel="manifest" href="/favicons/site.webmanifest" />
        <style type="text/css">
            body { overflow: hidden; background-color: #000; }
        </style>
    </head>
    <body>
        <canvas id="error"></canvas>
        <script type="text/javascript">
            let canvas = document.getElementById('error'),
                ctx = canvas.getContext('2d'),
                height,
                width,
                particles;

            const step = 10;

            let init = () => {
                height = window.innerHeight;
                width = window.innerWidth;

                canvas.height = height;
                canvas.width = width;

                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, width, height);
                ctx.fillStyle = '#000';

                const fontSize = Math.min(height,width)/2;

                ctx.font = `${fontSize}px Arial`;
                ctx.textAlign = 'center';
                ctx.fillText('404', width/2, height/2 + fontSize/4);

                const data    = ctx.getImageData(0, 0, width, height).data;
                const data32  = new Uint32Array(data.buffer);

                particles = [];

                for (let x = 0; x < width; x += step) {
                    for (let y = 0; y < height; y += step) {
                        const color = data32[y * width + x];

                        if (color != 0xFFFFFFFF) {
                            particles.push({ x, y });
                        }
                    }
                }
            }

            init();

            window.onresize = init;

            let counter = 0;

            function drawIt() {
                ctx.fillStyle = '#000000';
                ctx.fillRect(0, 0, width, height);

                for (let i = 0; i < particles.length; i++) {
                    const dX = (step/10) * Math.cos(i * 11 + counter),
                        dY = (step/10) * Math.sin(i * 13 + counter),
                        radius = step * 0.5 + dX - dY;

                    ctx.beginPath();
                    ctx.arc(
                        particles[i].x + dX,
                        particles[i].y + dY,
                        radius,
                        0, 2 * Math.PI,
                        false);

                    const color = (counter + 15 * (5 + dX - dY)) % 360;

                    ctx.fillStyle = `hsl(${color}, 100%, 50%)`;
                    ctx.fill();
                }

                counter += .1;

                requestAnimationFrame(drawIt);
            }

            drawIt();
        </script>
    </body>
</html>
