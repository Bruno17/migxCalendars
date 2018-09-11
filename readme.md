migxCalendars Extra for MODx Revolution
=======================================

A MIGXdb-based Calendar-Extra.

### Documentation

#### Requirements
MIGX – min-version: 2.6.8 pl 

MIGXangular – min-version: 1.0.0 beta3

#### Installation & Setup
Install the package by MODX package-management

Duplicate both Templates under the category migxCalendars

Create two Resources

One for viewing the calendar with the template, which you have duplicated from 
migxcal_baseTemplate

One for frontend-editing the calendar with the template, which you have duplicated from 
migxcal_editableTemplate

Create a ajax-resource, where we fetch the datas for the calendars from.
Name it for example: get-events
Choose a blank-template for this one.
The Content for this one:

```
[[!migxcalGetEvents]]
``` 
Put the ID of this resource in both templates into the migxcalCalendar-snippet-call here:
```
&ajax_id=`3`
```

Go to Components->MIGX Tab ‘MIGX’
Click ‘Import from packages’ – put into the dialog-popup: migxcalendars
Click ‘Ok’

Go to the tab ‘Package Manager’
Put into Package Name: migxcalendars
Inside the Tab ‘Create Tables’ click ‘Create Tables’

Now we should be ready to add some categories.

Go to the Main-menue ‘migxCalendars’
Add some Categories.

#### Show as list
If you'd like to show the calender as a list, you'd best look into [migxLoopCollection](https://github.com/Bruno17/MIGX/wiki/migxLoopCollection)
```
[[migxLoopCollection]]
```

#### Creating Dates
Visit the Calendar-Frontend-Editing-Page while you are logged into the manager.

To Create new Date-Containers, drag a Category from the left-side into the calendar-view.
To Add multiple Dates into one Date-Container, you need to load one or more Date Containers into the left-side before.

Click either on one of the Dates and click the ‘Open’ – Button
or
Click the button ‘Load Container’ and search/select one

Now you can drag Date-Containers into the calendar-view.
This will add new Dates into the dragged Date-container.

This is not possible with a Date – Container with repeating-dates.
Repeating Dates are created by clicking the ‘repeating’ – checkbox and selecting a start/end – repeating-date

It is possible to publish/unpublish repeating-dates individually.

Have fun playing around.


---

**Author:** Bruno Perner b.perner@gmx.de [webcmsolutions.de](http://www.webcmsolutions.de)
