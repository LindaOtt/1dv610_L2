# Login_1DV608
Interface repository for 1DV608 assignment 2 and 4


I have finished test cases 1.1 to 4.6. 
The repository can be downloaded and run on a local server for testing.
Below are descriptions of two added test cases.

## Test case 5.1, Show time zone dropdown
Normal navigation to page, page is shown.

**Input:**
- Test case 1.1.

**Output:**
- The text "Not logged in", is shown
- A form for login is shown
- Todays date and time is shown in correct format.
- Underneath today's date and time, there is a dropdown list with five different time zones. The default timezone "Europe/Stockholm" is picked.
- Next to the dropdown list is some text that says "Pick timezone".
- Next to the text is a submit button that says "Change".

## Test case 5.2, Change time zone

**Input:**
- Test case 5.1.
- Pick a timezone from the list
- Click the "Change" button

**Output:**
- The time is updated and reflects the picked timezone.
- The weekday and date are updated and reflect the picked timezone.
- The dropdown list shows the picked timezone.