# Background

_You just joined Mobility Work and your first job is to refactor an existing class. 
You are alone on this and you may not ask to anyone any help except the given code.
This class is part of a Symfony 2/3/4 project, but that doesn't matter._


✨ **Give a list of prioritised issues that need to be refactored from the most important to the less valuable from an OOP perspective.**

Try to apply best PHP and **OOP** practices, and justify your choices.

_Carefully read the existing implementation, and make it solid._

Hotel:
- name: string
- address: string
- currency: Currency

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