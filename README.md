
## About TMI

TMI is a time tracker designed to help you stay on track during a consultancy.

- Can be connected to Gitlab to fetch projects and tasks
- If you have contracting slots, you can define a weekly schedule
- Tracking is done server side, start your timer on one device and continue on the other
- Scrum ready, tag meetings with scrum specific naming  
- Define your custom labels


## Mapping to ontologies

- tmi:Meeting => schema:Event => 
|- scrum:Daily == scrum:Standup  == scrum:DailyScrum
|- scrum:Planning == scrum:SprintPlanning
|- scrum:Review == scrum:SprintReview
|- scrum:Retrospective == scrum:SprintRetrospective
|- scrum:Grooming == scrum:ProductBacklogRefinement == scrum:BacklogRefinement

- schema:ReviewAction || schema:OrganizeAction
|- tmi:RefinementAction, the action to refine the backlog or an issue, if done alone and not in a meeting

|- scrum:Sprint

- tmi:Task inherit from schema:CreativeWork and schema:Action
 -- startTime
 -- endTime
 -- duration: schema:Duration
 -- agent: Person => User
 -- identifier => UUID
 -- description => 
 -- object:Thing => schema:Project || schema:CreativeWork
 -- actionStatus => ActionStatusType => CompletedActionStatus or ActiveActionStatus


## License

TMI is open-sourced software licensed under the [AGPL-3.0 license](https://opensource.org/licenses/AGPL-3.0).
