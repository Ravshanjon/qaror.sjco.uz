<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SHOW PAGE</title>
</head>
<body>

<h1>SHOW PAGE ISHLAYAPTI</h1>
<p>{{ $qaror->title }}</p>

<iframe
    src="{{ asset('storage/' . $qaror->pdf_path) }}"
    class="w-full h-[80vh]"
></iframe>

</body>
</html>
