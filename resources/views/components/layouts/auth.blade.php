<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SimaluCoffee</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#33231d',
                        'primary-container': '#4B3832',
                        'surface': '#FAF7F5',
                        'surface-dim': '#F0EBE8',
                        'on-primary': '#FFFFFF',
                        'on-surface': '#1C1B1F',
                        'on-surface-variant': '#6B6560',
                        'outline': '#D4CFC9',
                        'outline-variant': '#E5E0DC',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-surface text-on-surface antialiased min-h-screen flex items-center justify-center">
    {{ $slot }}
</body>
</html>
