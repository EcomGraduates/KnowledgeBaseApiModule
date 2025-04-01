# Knowledge base API module for FreeScout
This module adds a public API for the [FreeScout](https://freescout.net) knowledge base with advanced analytics, custom URL support, and a testing interface.

## Requirements
- [FreeScout](https://freescout.net) installed 
- FreeScout [Knowledge base module](https://freescout.net/module/knowledge-base/) 

## Installation

1. Download the latest module zip file via the releases card on the right.
2. Transfer the zip file to the server in the Modules folder of FreeScout.
3. Unpack the zip file.
4. Remove the zip file.
5. Activate the module via the Modules page in FreeScout.
6. Go to settings and access the Knowledge Base API menu option
7. Enter a custom token or generate a new one. Then press save

![image](https://github.com/user-attachments/assets/71f73062-7dec-4998-827b-256e05799778)
![image](https://github.com/user-attachments/assets/91bc46e9-99e2-4771-8d60-5057ad3090e8)

## Update instructions

1. Download the latest module zip file via the releases card on the right.
2. Transfer the zip file to the server in the Modules folder of FreeScout.
3. Remove the folder KnowledgeBaseApiModule
4. Unpack the zip file.
5. Remove the zip file.

## Features

### API Authentication
- Secure token-based authentication for all API requests
- Easy token generation via the administration interface

### Custom URL Templates
- Customize how article and category URLs are returned in API responses
- Support for both API URLs and client-facing URLs
- Placeholders for mailbox ID, category ID, and article ID: `[mailbox]`, `[category]`, `[article]`
- Leave empty to use default FreeScout knowledge base URLs

### Analytics Dashboard
- View comprehensive usage statistics for your knowledge base
- Track article and category views with detailed metrics
- Monitor search performance and success rates
- Visualize data with customizable charts (bar/pie) and tabbed interface
- Identify your most popular content and search terms

### Built-in API Testing Interface
- Test all API endpoints directly from the administration interface
- Interactive "Try it out" section to experiment with different parameters
- View real-time API responses
- Example curl commands and JavaScript code for all endpoints

### Content Tracking
- Automatic tracking of article and category views
- Search query tracking with result statistics
- Popular content identification

## API Endpoints

### Get all categories in a mailbox
```
GET /api/knowledgebase/{mailbox}/categories?token=YOUR_TOKEN
```

### Get articles in a category
```
GET /api/knowledgebase/{mailbox}/categories/{categoryId}?token=YOUR_TOKEN
```

### Get a specific article within a category
```
GET /api/knowledgebase/{mailbox}/categories/{categoryId}/articles/{articleId}?token=YOUR_TOKEN
```

### Search for articles by keyword
```
GET /api/knowledgebase/{mailbox}/search?q=keyword&token=YOUR_TOKEN
```

### Get most popular articles and categories
```
GET /api/knowledgebase/{mailbox}/popular?token=YOUR_TOKEN
```

### Export all KB content for AI training
```
GET /api/knowledgebase/{mailbox}/export?token=YOUR_TOKEN
```

## Query Parameters

| Parameter | Description | Required | Default |
|-----------|-------------|----------|---------|
| token | Your API token for authentication | Yes | - |
| format | Response format: json or xml | No | json |
| q | Search query keyword (required for search endpoint) | No | - |
| locale | Optional locale for returned content | No | mailbox default locale |
| limit | Maximum number of results to return | No | 5 |
| type | Filter type for popular endpoint (all, articles, categories) | No | all |
| include_hidden | Include hidden/unpublished content in export endpoint | No | false |

## Usage Examples

### Curl example to retrieve categories:
```
curl -X GET "https://example.com/api/knowledgebase/1/categories?token=YOUR_TOKEN" \
     -H 'Accept: application/json' \
     -H 'Content-Type: application/json; charset=utf-8'
```

### JavaScript example with fetch:
```javascript
fetch("https://example.com/api/knowledgebase/1/categories?token=YOUR_TOKEN")
  .then(response => response.json())
  .then(data => console.log(data));
```

### Search example:
```
curl -X GET "https://example.com/api/knowledgebase/1/search?q=help&token=YOUR_TOKEN" \
     -H 'Accept: application/json' \
     -H 'Content-Type: application/json; charset=utf-8'
```

### Get popular articles with limit:
```
curl -X GET "https://example.com/api/knowledgebase/1/popular?limit=5&type=articles&token=YOUR_TOKEN" \
     -H 'Accept: application/json' \
     -H 'Content-Type: application/json; charset=utf-8'
```

### Export all content:
```
curl -X GET "https://example.com/api/knowledgebase/1/export?token=YOUR_TOKEN" \
     -H 'Accept: application/json' \
     -H 'Content-Type: application/json; charset=utf-8'
```

## Credits
This module was originally created by [jtorvald](https://github.com/jtorvald/) and has been extended with additional features by EcomGraduates. The original repository can be found at: https://github.com/jtorvald/freescout-knowledge-api

## Contributing

Feel free to add your own features by sending a pull request.

## Changelog

### 2.0.0
- Added article and category view tracking functionality
- Implemented analytics dashboard showing top categories and articles
- Enhanced analytics UI with Chart.js visualizations (bar/pie charts with toggle options)
- Added tabbed interface with statistics summaries
- Implemented search tracking (queries, results, and success rates)
- Added new API endpoints:
  - `/popular` to retrieve most viewed content
  - `/export` to output all KB content for AI training
- Added custom URL functionality to use API module's client URL templates in KB module
- Fixed reference search to properly use custom client URLs
- Added built-in API testing interface for all endpoints
- Comprehensive documentation in settings page

### 1.0.2
- Updated by EcomGraduates to add access token features

### 1.0.1
- Initial release with REST API functionality (no token authentication)

## LICENSE

MIT