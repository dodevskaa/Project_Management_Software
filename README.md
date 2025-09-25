# Project Management Software Specification

## Description
This project management software will serve as a simplified task and project management system. The system allows for a hierarchical structure where users are categorized by experience level: **Senior**, **Mid**, and **Junior**, with **Admins** having full system control. A subset of Senior users can be designated as Team Leads, who are responsible for managing teams and projects.

This project can be divided into 2 sections, the very application visible to every user once they log in, and an Admin version that will be only available to the user designated as an Admin.

Projects represent the largest resource handled by the app. The project is assigned to a Senior Team Lead. The **Team Lead** has to then assign developers to this project i.e. create a team. Within the project the Team Lead can create tasks which can then be assigned to the team members to complete.

Tasks represent the main working matter in the app. A task (or commonly known as a Ticket) is created to describe the needed action to be taken.These actions can be anything from fixing a small bug to implementing a major feature to the project. The task has multiple statuses that are explained further in this document. When a task is initially created by the team lead it is not assigned to any project member. After member assignment the status of a task can be changed by the members (read carefully the permission hierarchy for task status changes).

Tasks can also receive comments, with full CRUD capabilities of the comment owner.

## User Roles and Permissions:

**Admin:**

Admins have the highest level of control over the system and manage users, projects, tasks, and comments. Admins can:
- Create and manage user accounts.
- Assign user experience levels (Senior, Mid, Junior).
- Designate Seniors as Team Leads.
- Create, update, assign, and delete projects with information such as:

  - Title
  - Description
  - Requirements
  - Estimated Time of Completion
  - Assignment to a Team Lead
  - Deadline

- View and manage all users, projects, tasks, and comments.
- Full CRUD (Create, Read, Update, Delete) access over:

  - User accounts
  - Projects
  - Tasks
  - Comments

_The Admin has full access to every part of the regular application a unique access to the admin panel. Within the regular application the admin has Senior Team Lead clearance to every existing project. Admin users do not need to have a dedicated registration form, you can add them directly in the Database._

**All Users:**

The following features are required for any user:
- User registration

  - Name/Nickname
  - Email
  - Password (Password in Database has to be hashed)
  - Repeat Password (Password in Database has to be hashed)
  - Level

- User Log in

  - Name - or - Email
  - Password

- Change password option

  - Enter old password
  - Enter new password

_The Non-Admin users can be created by the Admin through the Admin Panel or can register themselves through a separate registration form. These users will need to be approved by an admin in order for them to be able to log into the app. Users created by the Admin in the Admin Panel are automatically approved and have random password string generated._

**Senior:**

Seniors are experienced users and can be split into:
- Team Lead: Seniors that are able to lead projects and manage teams.
- Senior: Can perform tasks on assigned projects but does not manage teams (does not lead projects).
  
**Team Lead:**
  
- Can be assigned projects by Admins.
- Create their own team using users from the Senior, Mid, and Junior pools.
- Can create, assign, and manage tasks within their assigned project with information:

  - Title
  - Description
  - Time of creation

- Can assign tasks to team members.
- Can change the status of a task to To Do, In Progress, QA, or Done
- Can mark the project as Done once all tasks are complete.
- Leave comments on any task (including unassigned tasks)

_A Team Lead is defined as a Senior who has been assigned the role of Team Lead by the Admin. An Admin can then assign created projects exclusively to Seniors that have been marked as Team Leads prior. Note that a Team Lead’s unique permissions and privileges are scoped (limited) only to the projects that have been assigned to them - meaning - a Team Lead can be a member of a team of a different project as a regular Senior and NOT have the same privileges (creation of tasks) because here he is not the owner i.e. the project was not assigned to him, but has been added by another Team Lead as a regular senior._

**Senior:**

- Cannot manage teams (add/remove members to project).
- Can create and manage tasks within assigned projects but only once they have been added to a team/project by a Team Lead.
- Can change the status of any task.
- Leave comments on any task (including unassigned tasks)

**Mid:**

Mid-level users have the following permissions:
- Cannot create new tasks, only interact with existing ones.
- Can only assign tasks to themselves or Junior users.
- Can change the status of a task only if it is already assigned to them or any task assigned to a Junior.
- Can leave comments on tasks they are involved in and tasks that are assigned to Junior.

**Junior:** 

Junior users have the most limited permissions:
- Cannot create new tasks, only interact with tasks already assigned to them by Team Lead/Senior/Mid.
- Cannot assign tasks to self or to others.
- Can change the status of a task only if it is already assigned to them.
- Can leave comments only to tasks assigned to themselves.

## Project and Task Management:

**Projects:**

- Created by the Admin with details such as title, description, requirements, estimated time of completion, and deadline.
- Projects are assigned to Team Leads by Admins.
- Only Team Leads can create a team and assign tasks to team members within the project.
- Every Member of the team of a project has access to the project information (defined by the admin during Project creation)

**Tasks:** 

- Tasks are created by Team Leads or regular Seniors.
- Tasks have the following statuses:

  - To Do (default status when created)
  - In Progress
  - QA
  - Done

- Team Leads can assign tasks to any team member (Themselves, Other Seniors, Mid, Junior).
- Senior can assign tasks to Themselves, Mid, Junior.
- Mid users can assign tasks only to themselves or Junior users.
- Junior users can only update task statuses assigned to them.

**Comments:** 

- Created tasks have a comment section
- Comments must have a timestamp of the time of creation
- Seniors (Team Leads and Non-Team Leads) can leave comments on any task, regardless if the tasks has/has not received an assignee.
- Mid can leave comments on tasks already assigned to them and to tasks assigned to Juniors
- Junior can leave comments only on tasks assigned to them
- Regardless of the User level, every comment owner can Edit and Delete their own comments

  - Edited comments should be marked as edited
  - No need to keep timestamps of edits, only on first comment creation

- Within the admin panel, an Admin has the clearance to full CRUD operations to any comment of any user
- Automatic Comments on task status change, for example:

  - [USERNAME] changed the status from To Do to In Progress (timestamp is handled same as a regular comment insertion)

**Teams:**

- Teams represent the group of users that are assigned to a Project.

_The handling of what a Team is in your project is more liberal, meaning you can choose what a Team represents within your project. A Team can be the group of Users that have been added to a project by a Team Lead - or - you can treat Teams as a separate entity/resource within your App and Database. If you go on this route then the Senior Team Lead should have a separate tool to create Teams and assign (connect) them to a Project that has been assigned to him._

## Bonus:

UI Feedback Implementation Implement intuitive UI feedback throughout the application to enhance the user experience. This includes:

- Displaying success and error messages for CRUD operations and form submissions.
- Showing loading indicators during actions like task status updates or project assignments.
- Providing real-time feedback for user interactions to ensure seamless and responsive navigation.
- Handling edge cases, such as invalid input or server errors, with clear and informative messages.

## Technology Stack:

- HTML/CSS (Bootstrap/Tailwind can be used if preferred).
- PHP (OOP preferred) for server-side logic, handling CRUD operations for users, projects, tasks, and comments.
- JavaScript (JQuery) for interactive UI elements.
- SQL for storing user data, projects, tasks, comments, and statuses.

_This project management software provides a clear hierarchy of permissions, with responsibilities distributed among Admins, Senior Team Leads, Seniors, Mid-level, and Junior users._
