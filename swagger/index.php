<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Teamway | Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="../assets/swagger-ui.css" />
    <link rel="stylesheet" type="text/css" href="../assets/index.css" />
    <link rel="icon" type="image/png" href="../assets/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="../assets/favicon-16x16.png" sizes="16x16" />
  </head>

  <body>
    
    <div id="swagger-ui"></div>
    <script src="../assets/swagger-ui-bundle.js" charset="UTF-8"> </script>
    <script src="../assets/swagger-ui-standalone-preset.js" charset="UTF-8"> </script>
    <script>
      SwaggerUIBundle({
        url: "../assets/swagger.yaml",
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],
        plugins: [
          SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "StandaloneLayout"
      });
    </script>
  </body>
</html>
