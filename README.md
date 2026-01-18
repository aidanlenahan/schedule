# RBR Schedule
Aidan Lenahan; 10-09-25; Honors Web and Mobile App Development<br><br>

This branch of this repository is the [furnished](https://https://schedule-web.fly.dev/) of the [prototype](https://aidanlenahan.github.io/schedule) version. The furnished version is hosted on [fly.io](https://fly.io) on a shared CPU and 256 MB RAM and uses a postgres and PHP free backend.

---

This program, using PHP and SQL, takes a school schedule from iCal in CSV form and then is formatted according to the parsing rules of the program. Then, it is imported into phpmyadmin and then serves the blocks, periods, and times according to the schedule.

## Database Overview

The `rbrschedule` database manages a rotating school schedule system with three main tables:

### rotatingday
Tracks the daily schedule calendar, mapping specific dates to their corresponding rotating day types. Each record contains:
- `id`: Unique identifier for each calendar entry
- `date`: The calendar date for the school day
- `day`: The rotating day identifier (A, B, C, D, MIDTERMS, FINALS, Review)
- `times`: The schedule type for that day (fullday, halfday, 2hr, testing, review)

This table contains the complete academic calendar from October 2025 through June 2026, including regular rotating days, midterm and final exam periods, and review days.

#### rotatingday Date Implementation
Exported an .ICS file from school calendar. Specially formatted dates en masse in spreadsheets program. From there, converted into .CSV file and uploaded into mySQL. Used JavaScript for parsing of dates, blocks, times, and day types.

### rotations
Defines the block sequence for each rotating day type. Each day (A, B, C, D) has a specific order in which blocks 1-8 are scheduled across six time slots:
- `day`: The rotating day identifier (A, B, C, or D)
- `block1` through `block6`: The block numbers scheduled for each time slot

This configuration ensures that all eight blocks rotate through the schedule over the four-day cycle.

### schedule
Contains the specific time ranges for different schedule types. Each record defines the timing for:
- `id`: Schedule type identifier
- `daytype`: The schedule format (fullday, halfday, 2hr, testing)
- `block1` through `block6`: Time ranges for each block period
- `mod1` and `mod2`: Morning and lunch module times
- `extra`: Additional scheduling information

Different day types accommodate full instructional days, half days, two-hour delays, and testing schedules with modified time blocks.
