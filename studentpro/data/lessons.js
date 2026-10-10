/**
 * Vetted, offline lesson content for supported curriculum topics.
 * Lesson text is rendered as text, never as generated HTML or raw Markdown.
 */
const LESSON_LIBRARY = {
  mathematics: [
    {
      matches: /rational|fraction|decimal|ratio|proportion|number systems|number sense|percentage/,
      title: 'Rational numbers and fractions',
      objectives: [
        'Compare positive and negative fractions using a common denominator.',
        'Add fractions by first making their denominators equal.',
        'Check that a fraction answer is simplified.'
      ],
      introduction: 'A rational number is any number that can be written as a fraction a/b, where a and b are integers and b is not zero. Fractions are easier to compare and combine when they describe equal-sized parts.',
      steps: [
        { title: 'Make equal-sized parts', explanation: 'To compare or add fractions, find a common denominator. Multiply the numerator and denominator by the same number so the fraction keeps its value.' },
        { title: 'Compare or combine the numerators', explanation: 'Once denominators match, compare the numerators or add them. Keep the common denominator.' },
        { title: 'Simplify and check', explanation: 'Divide numerator and denominator by their greatest common factor. For a comparison, check the sign and number line position.' }
      ],
      concepts: [
        { term: 'Numerator', definition: 'The top number; it counts how many equal parts are being considered.' },
        { term: 'Denominator', definition: 'The bottom number; it tells how many equal parts make one whole.' },
        { term: 'Equivalent fractions', definition: 'Fractions with different numbers that name the same value, such as 1/2 and 2/4.' }
      ],
      examples: [
        { title: 'Compare two negative fractions', problem: 'Which is smaller: −3/4 or −2/3?', steps: ['Use denominator 12: −3/4 = −9/12 and −2/3 = −8/12.', 'On a number line, −9/12 is to the left of −8/12.'], answer: '−3/4 is smaller.' },
        { title: 'Add fractions with different denominators', problem: 'Find 2/3 + 3/4.', steps: ['The least common denominator of 3 and 4 is 12.', 'Convert: 2/3 = 8/12 and 3/4 = 9/12.', 'Add the numerators: 8/12 + 9/12 = 17/12.'], answer: '17/12, or 1 5/12.' }
      ],
      application: 'If a recipe needs 2/3 cup of one ingredient and 3/4 cup of another, converting both amounts to twelfths makes the total easy to measure: 17/12 cups.',
      summary: ['Use a common denominator to compare or add fractions.', 'For negative numbers, the value farther left on the number line is smaller.', 'Simplify the final fraction when possible.'],
      practice: [
        { question: 'What is 1/2 + 1/3?', options: ['2/5', '5/6', '2/6'], answerIndex: 1, feedback: 'Use sixths: 1/2 = 3/6 and 1/3 = 2/6, so the sum is 5/6.' },
        { question: 'Which number is greater: −1/2 or −3/5?', options: ['−1/2', '−3/5', 'They are equal'], answerIndex: 0, feedback: '−1/2 = −5/10 and −3/5 = −6/10. −5/10 is farther right on the number line.' }
      ]
    },
    {
      matches: /equation|algebra|variable|linear/,
      title: 'Solving a linear equation',
      objectives: ['Identify the unknown variable and the operations applied to it.', 'Use inverse operations to isolate a variable.', 'Check a solution by substituting it into the original equation.'],
      introduction: 'An equation says that two expressions have the same value. Solving it means finding the value of the variable that keeps both sides equal. Whatever operation you do to one side, do to the other.',
      steps: [
        { title: 'Simplify each side', explanation: 'Expand brackets if needed and combine like terms.' },
        { title: 'Undo addition or subtraction', explanation: 'Use the opposite operation on both sides to move constant terms away from the variable.' },
        { title: 'Undo multiplication or division', explanation: 'Divide or multiply both sides so the variable is by itself.' },
        { title: 'Check the result', explanation: 'Replace the variable in the original equation and confirm both sides match.' }
      ],
      concepts: [
        { term: 'Variable', definition: 'A letter, such as x, that represents an unknown or changeable number.' },
        { term: 'Inverse operations', definition: 'Operations that undo each other: addition/subtraction and multiplication/division.' },
        { term: 'Like terms', definition: 'Terms with the same variable part, such as 3x and 5x.' }
      ],
      examples: [
        { title: 'Solve a two-step equation', problem: 'Solve 3x + 7 = 28.', steps: ['Subtract 7 from both sides: 3x = 21.', 'Divide both sides by 3: x = 7.', 'Check: 3(7) + 7 = 28.'], answer: 'x = 7.' },
        { title: 'Solve an equation with brackets', problem: 'Solve 2(x + 3) = 18.', steps: ['Divide both sides by 2: x + 3 = 9.', 'Subtract 3 from both sides: x = 6.', 'Check: 2(6 + 3) = 18.'], answer: 'x = 6.' }
      ],
      application: 'A taxi charges a fixed ₹40 booking fee plus ₹12 per kilometre. If the fare is ₹124, the equation 40 + 12k = 124 helps find the distance: k = 7 km.',
      summary: ['Keep both sides balanced.', 'Undo operations in reverse order.', 'Substitute the answer to verify it.'],
      practice: [
        { question: 'Solve 4x − 5 = 11.', options: ['x = 4', 'x = 6', 'x = 16'], answerIndex: 0, feedback: 'Add 5 to get 4x = 16, then divide by 4: x = 4.' },
        { question: 'Solve 3x + 2 = 17.', options: ['x = 3', 'x = 5', 'x = 19'], answerIndex: 1, feedback: 'Subtract 2 to get 3x = 15, then divide by 3: x = 5.' }
      ]
    },
    {
      matches: /geometry|mensuration|area|perimeter|quadrilateral|triangle|rectangle/,
      title: 'Area and perimeter of common shapes',
      objectives: ['Distinguish area from perimeter.', 'Choose the correct formula for a rectangle or triangle.', 'Include the correct units in a measurement answer.'],
      introduction: 'Perimeter measures the distance around a flat shape. Area measures how much surface the shape covers. The two measurements answer different questions, so they use different formulas and units.',
      steps: [
        { title: 'Identify what is being measured', explanation: 'A boundary or fence needs perimeter. A surface to cover needs area.' },
        { title: 'Choose a formula', explanation: 'Rectangle: area = length × width; perimeter = 2 × (length + width). Triangle: area = (base × perpendicular height) ÷ 2.' },
        { title: 'Substitute measurements and units', explanation: 'Write the numbers into the formula. Perimeter uses units such as cm; area uses square units such as cm².' }
      ],
      concepts: [
        { term: 'Perimeter', definition: 'The total length around the boundary of a two-dimensional shape.' },
        { term: 'Area', definition: 'The number of square units that cover a two-dimensional surface.' },
        { term: 'Perpendicular height', definition: 'The shortest distance from a base to the opposite vertex or side, meeting the base at 90°.' }
      ],
      examples: [
        { title: 'Find a rectangle’s area and perimeter', problem: 'A rectangular board is 8 cm long and 3 cm wide.', steps: ['Area = 8 × 3 = 24.', 'Perimeter = 2 × (8 + 3) = 22.'], answer: 'Area = 24 cm²; perimeter = 22 cm.' },
        { title: 'Find a triangle’s area', problem: 'A triangle has base 10 m and perpendicular height 6 m.', steps: ['Area = (base × height) ÷ 2.', 'Area = (10 × 6) ÷ 2 = 30.'], answer: '30 m².' }
      ],
      application: 'To buy paint for a tabletop, use its area to estimate the surface to cover. To put a protective strip around its edge, use the perimeter.',
      summary: ['Perimeter measures around; area measures the surface.', 'Use the triangle formula only with a perpendicular height.', 'Area is written in square units.'],
      practice: [
        { question: 'What is the area of a 7 cm by 4 cm rectangle?', options: ['11 cm²', '22 cm²', '28 cm²'], answerIndex: 2, feedback: 'Rectangle area = length × width = 7 × 4 = 28 cm².' },
        { question: 'A triangle has base 8 cm and height 5 cm. What is its area?', options: ['20 cm²', '40 cm²', '13 cm²'], answerIndex: 0, feedback: 'Triangle area = (8 × 5) ÷ 2 = 20 cm².' }
      ]
    }
  ],
  physics: [
    {
      matches: /motion|kinematic|velocity|speed|acceleration|force|newton/,
      title: 'Describing motion with speed and acceleration',
      objectives: ['Distinguish distance, speed, velocity, and acceleration.', 'Use speed = distance ÷ time with consistent units.', 'Interpret a simple change in velocity.'],
      introduction: 'Motion describes how an object’s position changes over time. Speed tells how quickly distance is covered; velocity also includes direction. Acceleration describes how quickly velocity changes.',
      steps: [
        { title: 'List the known quantities', explanation: 'Write down distance, time, starting velocity, or final velocity and include units.' },
        { title: 'Select the relationship', explanation: 'For steady motion, speed = distance ÷ time. For uniform acceleration, final velocity = starting velocity + (acceleration × time).' },
        { title: 'Substitute and calculate', explanation: 'Keep units consistent, carry out the arithmetic, and include the result unit.' },
        { title: 'Check whether the result makes sense', explanation: 'A longer time at the same speed should mean more distance. Acceleration has units of metres per second squared.' }
      ],
      concepts: [
        { term: 'Speed', definition: 'Distance travelled per unit time; it has magnitude but no direction.' },
        { term: 'Velocity', definition: 'Displacement per unit time, including direction.' },
        { term: 'Acceleration', definition: 'Change in velocity per unit time, measured in m/s².' }
      ],
      examples: [
        { title: 'Calculate steady speed', problem: 'A cyclist travels 150 m in 30 s. What is the average speed?', steps: ['Use speed = distance ÷ time.', 'Speed = 150 m ÷ 30 s = 5 m/s.'], answer: '5 m/s.' },
        { title: 'Find final velocity', problem: 'A cart starts at 2 m/s and accelerates at 3 m/s² for 4 s.', steps: ['Use v = u + at.', 'Substitute: v = 2 + (3 × 4) = 14 m/s.'], answer: '14 m/s in the direction of its acceleration.' }
      ],
      application: 'A cyclist can estimate the time for a familiar route by dividing its distance by average speed, then allow extra time for stops and traffic.',
      summary: ['Speed = distance ÷ time.', 'Velocity includes direction; speed does not.', 'Acceleration tells how velocity changes over time.'],
      practice: [
        { question: 'A runner covers 100 m in 20 s. What is the average speed?', options: ['2 m/s', '5 m/s', '20 m/s'], answerIndex: 1, feedback: 'Speed = 100 ÷ 20 = 5 m/s.' },
        { question: 'A toy car speeds up from 1 m/s at 2 m/s² for 3 s. Its final velocity is…', options: ['5 m/s', '6 m/s', '7 m/s'], answerIndex: 2, feedback: 'Use v = u + at = 1 + (2 × 3) = 7 m/s.' }
      ]
    },
    {
      matches: /energy|work|power|kinetic|potential/,
      title: 'Work and energy',
      objectives: ['Explain energy as the ability to cause change.', 'Calculate kinetic energy in a simple case.', 'Describe an energy transfer without claiming energy disappears.'],
      introduction: 'Energy can be transferred between objects and changed from one form to another. In a closed system, the total energy is conserved even when some spreads out as heat or sound.',
      steps: [
        { title: 'Identify the energy store', explanation: 'A moving object has kinetic energy. An object raised above the ground has gravitational potential energy.' },
        { title: 'Choose a relationship', explanation: 'Kinetic energy = ½ × mass × speed². Use kilograms for mass and metres per second for speed to get joules.' },
        { title: 'Describe the transfer', explanation: 'State where energy starts and where it goes, such as gravitational potential energy changing into kinetic energy as an object falls.' }
      ],
      concepts: [
        { term: 'Kinetic energy', definition: 'Energy an object has because it is moving.' },
        { term: 'Potential energy', definition: 'Stored energy due to position or arrangement.' },
        { term: 'Joule', definition: 'The SI unit used to measure energy and work.' }
      ],
      examples: [
        { title: 'Calculate kinetic energy', problem: 'A 2 kg ball moves at 3 m/s. Find its kinetic energy.', steps: ['Use KE = ½mv².', 'KE = ½ × 2 × 3² = 9 J.'], answer: '9 J.' },
        { title: 'Describe a falling object', problem: 'A book falls from a shelf. What happens to its energy?', steps: ['At the shelf, the book has gravitational potential energy.', 'As it falls, its height decreases and its speed increases.', 'Potential energy is transferred mainly to kinetic energy; some energy becomes sound and heat on impact.'], answer: 'Energy changes form; it is not simply lost.' }
      ],
      application: 'A hydroelectric station uses water stored at height. As water flows down, energy transfer turns turbines that drive electrical generators.',
      summary: ['Moving objects have kinetic energy.', 'Use consistent SI units in calculations.', 'Describe energy as transferred or transformed, not destroyed.'],
      practice: [
        { question: 'What is the kinetic energy of a 1 kg object moving at 4 m/s?', options: ['4 J', '8 J', '16 J'], answerIndex: 1, feedback: 'KE = ½ × 1 × 4² = 8 J. Check carefully: 4² is 16 and half is 8 J.' },
        { question: 'As a ball rolls down a hill, gravitational potential energy is mainly transferred into…', options: ['Kinetic energy', 'New matter', 'No other energy store'], answerIndex: 0, feedback: 'The ball speeds up as it descends, so gravitational potential energy is transferred mainly into kinetic energy.' }
      ]
    }
  ],
  science: [
    {
      matches: /state|matter|particle|solid|liquid|gas|boiling|melting/,
      title: 'States of matter and particle motion',
      objectives: ['Describe how particles are arranged in solids, liquids, and gases.', 'Explain melting and evaporation using particle motion.', 'Distinguish a change of state from a chemical change.'],
      introduction: 'Matter is made of tiny particles. How these particles are arranged and how freely they move helps explain the properties of solids, liquids, and gases.',
      steps: [
        { title: 'Compare particle arrangements', explanation: 'In a solid, particles are close together and vibrate in place. In a liquid, they remain close but can move past one another. In a gas, they are far apart and move freely.' },
        { title: 'Add or remove thermal energy', explanation: 'Heating usually increases particle motion. Cooling usually reduces it.' },
        { title: 'Name the change of state', explanation: 'Melting changes solid to liquid; freezing changes liquid to solid; evaporation or boiling changes liquid to gas; condensation changes gas to liquid.' }
      ],
      concepts: [
        { term: 'Particle model', definition: 'A model that explains matter as tiny particles in constant motion.' },
        { term: 'Melting', definition: 'A physical change from solid to liquid as particles gain enough energy to move around one another.' },
        { term: 'Condensation', definition: 'A change from gas to liquid as particles lose energy and come closer together.' }
      ],
      examples: [
        { title: 'Ice melting in a glass', problem: 'What happens to water particles as an ice cube melts?', steps: ['The ice absorbs thermal energy from its surroundings.', 'Its particles vibrate more until they can move past one another.', 'The substance remains water; only its state changes.'], answer: 'Solid water becomes liquid water in a physical change.' },
        { title: 'Droplets on a cold bottle', problem: 'Where do droplets on the outside of a chilled bottle come from?', steps: ['Water vapour in the air touches the cold bottle.', 'The vapour loses thermal energy.', 'Some particles slow down and gather as liquid droplets.'], answer: 'The droplets form by condensation of water vapour in the air.' }
      ],
      application: 'A lid on a cooking pot catches steam. The steam cools on the lid, condenses into water, and may drip back into the pot.',
      summary: ['Particle spacing and movement differ in each state.', 'Heating and cooling can change particle motion and state.', 'A change of state does not create a new substance.'],
      practice: [
        { question: 'Which statement best describes particles in a liquid?', options: ['They are fixed in place.', 'They are close together and can move past one another.', 'They are far apart and move freely.'], answerIndex: 1, feedback: 'Liquid particles stay close together but are not fixed; they slide past each other.' },
        { question: 'Water vapour forming liquid drops on a cool surface is called…', options: ['Melting', 'Freezing', 'Condensation'], answerIndex: 2, feedback: 'Gas changing to liquid is condensation.' }
      ]
    }
  ],
  accountancy: [
    {
      matches: /account|balance sheet|asset|liabilit|capital|equity|journal|ledger|debit|credit/,
      title: 'The accounting equation',
      objectives: ['Classify a basic item as an asset, liability, or owner’s equity.', 'Use Assets = Liabilities + Owner’s Equity.', 'Explain why both sides remain balanced after a transaction.'],
      introduction: 'Accounting records what a business owns and owes. The accounting equation is the foundation of a balance sheet: the resources of a business are financed either by creditors or by its owner.',
      steps: [
        { title: 'Identify the business resources', explanation: 'Assets include cash, equipment, inventory, and amounts customers owe the business.' },
        { title: 'Identify claims on those resources', explanation: 'Liabilities are amounts owed to others. Owner’s equity is the owner’s remaining claim.' },
        { title: 'Check the equation', explanation: 'Add liabilities and owner’s equity. Their total must equal assets.' }
      ],
      concepts: [
        { term: 'Asset', definition: 'A resource controlled by the business that has value, such as cash or equipment.' },
        { term: 'Liability', definition: 'An amount the business is required to pay to another party.' },
        { term: 'Owner’s equity', definition: 'The owner’s claim on the business after liabilities are deducted from assets.' }
      ],
      examples: [
        { title: 'Find owner’s equity', problem: 'A shop has assets of ₹80,000 and liabilities of ₹25,000.', steps: ['Start with Assets = Liabilities + Owner’s Equity.', 'Rearrange: Owner’s Equity = Assets − Liabilities.', '₹80,000 − ₹25,000 = ₹55,000.'], answer: 'Owner’s equity is ₹55,000.' },
        { title: 'Record an owner investment', problem: 'The owner invests ₹10,000 cash in the business.', steps: ['Cash, an asset, increases by ₹10,000.', 'Owner’s equity also increases by ₹10,000.', 'Both sides rise by the same amount, so the equation stays balanced.'], answer: 'Assets increase ₹10,000; owner’s equity increases ₹10,000.' }
      ],
      application: 'A business owner can use the equation to see whether equipment was funded by a loan or by the owner’s investment.',
      summary: ['Assets are financed by liabilities and owner’s equity.', 'The equation must balance after every recorded transaction.', 'Classify items before calculating.'],
      practice: [
        { question: 'Assets are ₹50,000 and liabilities are ₹18,000. What is owner’s equity?', options: ['₹32,000', '₹68,000', '₹18,000'], answerIndex: 0, feedback: 'Equity = assets − liabilities = ₹50,000 − ₹18,000 = ₹32,000.' },
        { question: 'A business buys equipment for cash. What happens immediately to total assets?', options: ['They increase by the full cost.', 'They decrease by the full cost.', 'One asset rises while cash falls by the same amount, so total assets stay the same.'], answerIndex: 2, feedback: 'Equipment rises and cash falls by the same amount, so total assets are unchanged at that moment.' }
      ]
    }
  ],
  javascript: [
    {
      matches: /javascript|array|function|closure|scope|program|coding|web|computer|algorithm/,
      title: 'JavaScript functions and reusable logic',
      objectives: ['Explain why a function is useful.', 'Trace arguments into parameters and a returned value.', 'Predict the result of a small function call.'],
      introduction: 'A function is a named block of code that can be run when needed. It helps avoid repeating steps and makes a program easier to understand and test.',
      steps: [
        { title: 'Define the function', explanation: 'Use function, a name, parentheses for parameters, and braces for the body.' },
        { title: 'Pass inputs', explanation: 'When calling the function, supply arguments. Each argument is received by its matching parameter.' },
        { title: 'Return a result', explanation: 'Use return when the caller needs a value. Code after return in that function call does not run.' },
        { title: 'Test with another input', explanation: 'Try a different argument to confirm the function works beyond one example.' }
      ],
      concepts: [
        { term: 'Parameter', definition: 'A named input listed in a function definition.' },
        { term: 'Argument', definition: 'The actual value supplied when a function is called.' },
        { term: 'Return value', definition: 'The value a function sends back to the code that called it.' }
      ],
      examples: [
        { title: 'Add two values', problem: 'function add(a, b) { return a + b; }  Calling add(4, 7) gives what result?', steps: ['The arguments 4 and 7 are assigned to parameters a and b.', 'The expression a + b becomes 4 + 7.', 'The function returns the result.'], answer: '11.' },
        { title: 'Convert minutes to seconds', problem: 'Write a function that converts a number of minutes to seconds.', steps: ['Use one parameter: minutes.', 'There are 60 seconds in each minute, so calculate minutes × 60.', 'Return that value; for example, 3 minutes gives 180 seconds.'], answer: 'function toSeconds(minutes) { return minutes * 60; }  toSeconds(3) returns 180.' }
      ],
      application: 'A shopping app can reuse a calculateTotal function for every basket instead of duplicating price-addition code on each screen.',
      summary: ['Functions package reusable instructions.', 'Arguments fill parameters when a function is called.', 'Return sends a calculated value back to the caller.'],
      practice: [
        { question: 'What does function double(n) { return n * 2; } return for double(6)?', options: ['8', '12', '36'], answerIndex: 1, feedback: 'The argument 6 becomes n, so n × 2 is 6 × 2 = 12.' },
        { question: 'In function greet(name) { return "Hi " + name; }, what is name?', options: ['An argument only', 'A parameter', 'The return value'], answerIndex: 1, feedback: 'name is the parameter in the function definition. A value such as "Asha" is an argument when calling greet("Asha").' }
      ]
    }
  ],
  python: [
    {
      matches: /python|loop|list|condition|variable|program|coding|data/,
      title: 'Python variables, conditions, and loops',
      objectives: ['Store a value in a clearly named variable.', 'Use an if/else condition to choose between actions.', 'Trace a short loop one iteration at a time.'],
      introduction: 'A program follows instructions. Variables keep values, conditions let a program make a choice, and loops repeat a set of instructions.',
      steps: [
        { title: 'Store a value', explanation: 'Use a descriptive name and assignment, such as score = 8. The equals sign assigns the value; it is not a comparison.' },
        { title: 'Make a decision', explanation: 'An if condition runs its indented block when the condition is true; else handles the other case.' },
        { title: 'Repeat safely', explanation: 'A for loop visits each item in a sequence. Track how many iterations occur and what changes each time.' }
      ],
      concepts: [
        { term: 'Variable', definition: 'A name referring to a value stored by a program.' },
        { term: 'Condition', definition: 'A true-or-false test that can control which block runs.' },
        { term: 'Iteration', definition: 'One pass through the body of a loop.' }
      ],
      examples: [
        { title: 'Choose a message', problem: 'score = 72. If the pass mark is 40, which branch runs?', steps: ['Test score >= 40.', '72 >= 40 is true.', 'The if block runs.'], answer: 'The program reports a pass.' },
        { title: 'Trace a loop', problem: 'total = 0; for n in [2, 3, 4]: total += n. What is total?', steps: ['Start with total = 0.', 'First iteration: 0 + 2 = 2.', 'Second iteration: 2 + 3 = 5.', 'Third iteration: 5 + 4 = 9.'], answer: 'total is 9.' }
      ],
      application: 'A program can loop through a list of shopping prices and add each one to a running total before displaying the bill.',
      summary: ['Variables store values under names.', 'Conditions select a path based on true or false.', 'A loop repeats its body for each item or count.'],
      practice: [
        { question: 'What values does list(range(3)) visit in a for loop?', options: ['1, 2, 3', '0, 1, 2', '0, 1, 2, 3'], answerIndex: 1, feedback: 'range(3) starts at 0 and stops before 3, so it visits 0, 1, and 2.' },
        { question: 'If x = 5, is x > 8 true or false?', options: ['True', 'False', 'It is an error'], answerIndex: 1, feedback: '5 is not greater than 8, so the condition is false.' }
      ]
    }
  ],
  english: [
    {
      matches: /english|tense|grammar|sentence|communication|writing|spoken|language|verb/,
      title: 'Choosing the correct verb tense',
      objectives: ['Recognize present, past, and future time clues.', 'Choose a verb form that agrees with the time of an event.', 'Edit a sentence to keep its tense consistent.'],
      introduction: 'Verb tense helps a reader understand when an action happens. Time words such as yesterday, every day, and tomorrow can guide the choice, but the whole sentence must make sense.',
      steps: [
        { title: 'Find the time clue', explanation: 'Look for words such as yesterday, now, usually, or next week.' },
        { title: 'Choose the matching form', explanation: 'Use a past form for a completed past action, a present form for a routine or current fact, and a future form for an event that has not happened yet.' },
        { title: 'Check agreement and consistency', explanation: 'Match the verb to its subject and avoid changing tense without a reason.' }
      ],
      concepts: [
        { term: 'Present tense', definition: 'A verb form used for current facts, routines, or actions happening now.' },
        { term: 'Past tense', definition: 'A verb form used for actions completed before now.' },
        { term: 'Future reference', definition: 'A way to describe a later event, often using will or going to.' }
      ],
      examples: [
        { title: 'Match a completed action', problem: 'Complete: “Yesterday, Mira ___ (visit) her grandmother.”', steps: ['The word yesterday places the visit in the past.', 'Use the past form of visit: visited.', 'Read the sentence again to check the time matches.'], answer: '“Yesterday, Mira visited her grandmother.”' },
        { title: 'Keep a routine in the present', problem: 'Correct: “Ravi walk to school every day.”', steps: ['Every day signals a routine in the present.', 'Ravi is third-person singular, so add -s to the present verb.', 'The corrected verb is walks.'], answer: '“Ravi walks to school every day.”' }
      ],
      application: 'In a school report, keeping tense consistent helps readers tell which work is already finished and which is still planned.',
      summary: ['Use time clues to identify when an action happens.', 'Match the verb form to the time and subject.', 'Keep tense consistent unless the time changes.'],
      practice: [
        { question: 'Choose the correct sentence for a usual routine.', options: ['She reads each evening.', 'She read each evening yesterday.', 'She will reads each evening.'], answerIndex: 0, feedback: 'Each evening describes a routine, so the simple present “reads” is correct for she.' },
        { question: 'Complete: “Tomorrow, we ___ the museum.”', options: ['visited', 'visit yesterday', 'will visit'], answerIndex: 2, feedback: 'Tomorrow points to the future: “will visit”.' }
      ]
    }
  ],
  reasoning: [
    {
      matches: /critical|evaluation|synthesis|reason|evidence|argument|analysis/,
      title: 'Evaluate a claim using evidence',
      objectives: ['Separate a claim from the evidence offered for it.', 'Check whether evidence is relevant and sufficient.', 'Write a conclusion that does not overstate what the evidence shows.'],
      introduction: 'Critical evaluation means checking how well reasons support a claim. It is not the same as disagreeing: a strong evaluation fairly describes the claim, examines the evidence, and notes what remains uncertain.',
      steps: [
        { title: 'State the claim precisely', explanation: 'Rewrite the main point in one sentence so you know exactly what is being asserted.' },
        { title: 'Identify the evidence', explanation: 'List the observations, data, examples, or sources offered in support.' },
        { title: 'Test the connection', explanation: 'Ask whether the evidence directly relates to the claim, comes from a reliable source, and is enough to support the conclusion.' },
        { title: 'Give a balanced conclusion', explanation: 'Say what the evidence supports and what additional information would be needed.' }
      ],
      concepts: [
        { term: 'Claim', definition: 'A statement or conclusion someone wants the reader to accept.' },
        { term: 'Evidence', definition: 'Information used to support or challenge a claim.' },
        { term: 'Relevance', definition: 'How directly a piece of evidence relates to the claim being evaluated.' }
      ],
      examples: [
        { title: 'Check a school transport claim', problem: 'Claim: “The new bus route makes every student’s journey shorter.” Evidence: three students on the route report shorter trips.', steps: ['The reports are relevant to those three students.', 'Three reports do not represent every student.', 'A before-and-after travel-time survey of all route users would be stronger evidence.'], answer: 'The evidence supports shorter journeys for some students, but not the claim about every student.' },
        { title: 'Synthesize two sources', problem: 'Source A reports that a library extended its hours. Source B reports more evening visits after the change.', steps: ['Both sources concern the same library and time period.', 'The change and the rise in visits are consistent with a link.', 'Without comparing other changes or visitor counts over time, the sources do not prove the longer hours alone caused the rise.'], answer: 'The sources suggest a connection, but more evidence is needed to establish cause.' }
      ],
      application: 'When reading a news post, check who made the claim, what data supports it, and whether the data actually measures the outcome being discussed.',
      summary: ['Name the claim before judging it.', 'Check evidence for relevance, reliability, and sufficiency.', 'Match the strength of your conclusion to the evidence.'],
      practice: [
        { question: 'A poll asks 12 members of one club whether all teenagers like a new app. What is the main limitation?', options: ['The sample is small and not representative of all teenagers.', 'The question uses a number.', 'A poll can never provide evidence.'], answerIndex: 0, feedback: 'A small group from one club may not represent all teenagers, so the broad conclusion is not justified.' },
        { question: 'A report finds more park visitors after new lights were installed. Does this alone prove the lights caused the increase?', options: ['Yes, because the events happened in that order.', 'No; other possible causes and comparisons should be checked.', 'Yes, because visitor counts are numbers.'], answerIndex: 1, feedback: 'Timing is relevant, but other changes could explain the increase. Comparisons or more data would strengthen a causal claim.' }
      ]
    }
  ]
};

const LessonContent = {
  getLesson(profile, topic) {
    const subjectId = String(profile.selectedSubjectId || '').toLowerCase();
    const subjectName = String(profile.selectedSubjectName || profile.courseName || profile.skillName || '').toLowerCase();
    const topicText = String(topic || '').toLowerCase();
    let domain = null;

    if (/math/.test(subjectId) || /mathematics|math/.test(subjectName)) domain = 'mathematics';
    else if (/python/.test(subjectId) || /python/.test(subjectName)) domain = 'python';
    else if (/javascript|web|bca/.test(subjectId) || /javascript|web development|computer science|computer applications|programming|technology/.test(subjectName)) domain = 'javascript';
    else if (/physics/.test(subjectName) || /physics/.test(subjectId)) domain = 'physics';
    else if (/science|sci/.test(subjectName) || /science|sci/.test(subjectId)) domain = 'science';
    else if (/account|commerce|finance/.test(subjectId) || /account|commerce|finance/.test(subjectName)) domain = 'accountancy';
    else if (/english|language/.test(subjectId) || /english|spoken language/.test(subjectName)) domain = 'english';
    else if (/general|reason|analysis/.test(subjectId) || /general knowledge|analytical reasoning|critical thinking/.test(subjectName)) domain = 'reasoning';

    if (!domain) return null;

    const lesson = LESSON_LIBRARY[domain].find(item => item.matches.test(topicText));
    if (!lesson) return null;

    const grade = Number(profile.standard);
    return {
      ...lesson,
      objectives: [...lesson.objectives],
      steps: lesson.steps.map(step => ({ ...step })),
      concepts: lesson.concepts.map(concept => ({ ...concept })),
      examples: lesson.examples.map(example => ({ ...example, steps: [...example.steps] })),
      summary: [...lesson.summary],
      practice: lesson.practice.map(question => ({ ...question, options: [...question.options] })),
      subject: profile.selectedSubjectName || profile.courseName || profile.skillName || '',
      grade: Number.isInteger(grade) && grade > 0 ? grade : null,
      module: topic
    };
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { LESSON_LIBRARY, LessonContent };
}
