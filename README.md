# Events Api

This project requires the use of [Slim Framework](https://www.slimframework.com/).

## Database Structure

| Name  | Type |
| ------------- | ------------- |
| ID  | int  |
| title  | varchar  |
| location  | varchar  |
| date  | date  |
| time  | time  |
| ageMin  | int  |
| ageMax  | int  |
| groupSize  | int  |
| limited  | int  |
| maxParticipants  | int  |
| joining  | int  |
| description  | varchar  |
| img  | varchar  |
| type  | varchar  |
| language  | varchar  |

## Endpoints

- `GET /events`
- `GET /event/:id`
- `POST /event`
- `PUT /event/:id`
