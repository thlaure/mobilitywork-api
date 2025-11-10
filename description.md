# Background

_You just joined Mobility Work and your first job is to refactor an existing class. 
You are alone on this and you may not ask to anyone any help except the given code.
This class is part of a Symfony 2/3/4 project, but that doesn't matter._


✨ **Give a list of prioritised issues that need to be refactored from the most important to the less valuable from an OOP perspective.**
1. Remove secrets from the code to avoid security issues and remove them from Git history if needed.
2. Massive code duplication -> not respecting DRY and Single Responsibility
3. NO MAGIC NUMBERS -> hard to maintain
4. Service manager and configuration coupling
5. No error handling -> may crash in production
6. Mixing business and infrastructure logic -> violates all the SOLID principles and clean architecture
7. Commented logs
8. Lack of type declaration -> the create methods don't need to return a boolean value
9. Tight coupling with Zendesk API -> hard to maintain and to test

Try to apply best PHP and **OOP** practices, and justify your choices.

_Carefully read the existing implementation, and make it solid._

Hotel:
- name: string
- address: string
- currency: Currency
- hotelContact: HotelContact

HotelContact:
- email: string

Currency:
- code: string

Reservation:
- room: Room
- hotel: Hotel
- bookedDate: DateTime
- roomPrice: float
- bookedStartTime: DateTime
- bookedEndTime: DateTime
- customer: Customer
- reference: string

Customer:
- simplePhoneNumber: string

Room:
- name: string
- type: string

Language:
- name: string