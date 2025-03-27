# Knowledge base API module for FreeScout
This module adds the option to add a public API for the [FreeScout](https://freescout.net) knowledge base (module).

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

## Credits
This module was originally created by [jtorvald](https://github.com/jtorvald/) and has been extended with additional features by EcomGraduates. The original repository can be found at: https://github.com/jtorvald/freescout-knowledge-api

## Contributing

Feel free to add your own features by sending a pull request.

## Get knowledge base categories in a mailbox

```
curl "https://example.com/api/knowledgebase/1/categories?locale=en&token=YOUR_TOKEN" \
-H 'Accept: application/json' \
-H 'Content-Type: application/json; charset=utf-8' \
-d $'{}'
```

## Get articles in a category

```
curl "https://example.com/api/knowledgebase/1/categories/1?locale=en&token=YOUR_TOKEN" \
     -H 'Accept: application/json' \
     -H 'Content-Type: application/json; charset=utf-8' \
     -d $'{}'
```
## Changelog

### 1.0.2
- Updated by EcomGraduates to add access token features

### 1.0.1
- Initial release with REST API functionality (no token authentication)

## LICENSE

MIT