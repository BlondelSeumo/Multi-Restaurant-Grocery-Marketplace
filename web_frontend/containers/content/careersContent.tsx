/* eslint-disable react/no-unescaped-entities */
import React from "react";
import cls from "./content.module.scss";
import { Career } from "interfaces";
import SupportCard from "components/supportCard/supportCard";

type Props = {
  data?: Career;
};

export default function CareersContent({ data }: Props) {
  return (
    <div className={`container ${cls.container}`}>
      <div className={cls.header}>
        <h1 className={cls.title}>{data?.title}</h1>
        <p className={cls.text}>
          {data?.category}, {data?.location}
        </p>
      </div>
      <main className={cls.content}>
        <div>
          <h3>
            <strong>About the role:</strong>
          </h3>
          <p>
            Partners with stakeholders and leads team efforts to build and
            maintain backend services and solutions to support user-facing
            products, downstream services, or infrastructure tools and platforms
            used across Uber.
          </p>
          <h3>
            <strong>About the Team:</strong>
          </h3>
          <p>
            If you are interested in making a direct impact to the company's
            bottom line, join the Ad Tech team. Uber is on track to spend
            hundreds of millions of dollars in marketing. A 10% improvement in
            ROI on this spend saves the company hundreds of millions of dollars
            and directly speeds up the company's path to profitability. Help us
            build the software systems that will make this happen. The team is
            still in its early stages, so you can drive products from inception
            to adoption and further iterations.
          </p>
          <p>
            \* Build and work with real-time services along with batch data
            pipelines that track and attribute ad spend to actions that Uber
            users take (e.g. take rides, eats orders, etc.).
          </p>
          <p>
            \* Work on systems that will use advanced machine learning
            techniques to optimize spend within different channels
          </p>
          <p>
            \* Build real time systems that can handle millions of TPS in sub
            10ms latency
          </p>
          <p>
            \* Build systems that detect ad fraud and help us eliminate ad
            networks that are only here to rob us of our money.
          </p>
          <p>
            \* Build systems that get us free traffic (SEO and content) instead
            of having us pay other ad networks for it.
          </p>
          <p>
            \* Build systems that will help us target the right kind of
            potential users.This involves working with datasets that are world
            population scale (billions of rows).
          </p>
          <h3>
            <strong>Minimum qualifications:</strong>
          </h3>
          <ul>
            <li>
              <p>
                PhD or equivalent in Computer Science, Engineering, Mathematics
                or related field <strong>OR</strong> 3-years full-time Software
                Engineering work experience, <strong>WHICH INCLUDES</strong>{" "}
                2-years total technical software engineering experience in one
                or more of the following areas:
              </p>
            </li>
            <li>
              <p>Programming language (e.g. C, C++, Java, Python, or Go)</p>
            </li>
            <li>
              <p>
                <em>
                  Note the 2-years total of specialized software engineering
                  experience may have been gained through education and
                  full-time work experience, additional training, coursework,
                  research, or similar (OR some combination of these).&nbsp; The
                  years of specialized experience are not necessarily in
                  addition to the years of Education &amp; full-time work
                  experience indicated.
                </em>
              </p>
            </li>
          </ul>
          <h3>
            <strong>Technical skills:</strong>
          </h3>
          <p>Preferred:</p>
          <ul>
            <li>Product engineering</li>
            <li>Scalability engineering</li>
            <li>Distributed systems</li>
            <li>Bonus Points: If you have prior adtech experience!</li>
          </ul>
          <p>
            At Uber, we ignite opportunity by setting the world in motion. We
            take on big problems to help drivers, riders, delivery partners, and
            eaters get moving in more than 600 cities around the world!
          </p>
          <p>
            We welcome people from all backgrounds who seek the opportunity to
            help build a future where everyone and everything can move
            independently. If you have a curiosity, passion and collaborative
            spirit, work with us, and let’s move the world forward, together!
          </p>
          <p>
            Uber is proud to be an Equal Opportunity/Affirmative Action
            employer. All qualified applicants will receive consideration for
            employment without regard to sex, gender identity, sexual
            orientation, race, color, religion, national origin, disability,
            protected Veteran status, age, or any other characteristic protected
            by law. We also consider qualified applicants regardless of criminal
            histories, consistent with legal requirements.
          </p>
          <p>
            If you have a disability or special need that requires
            accommodation, please let us know by completing{" "}
            <a href="https://forms.gle/aDWTk9k6xtMU25Y5A">this form</a>.
          </p>
          <p>
            For New York, NY-based roles: The base salary range for this role is
            $174,000 per year - $193,500 per year.
          </p>
          <p>
            You will be eligible to participate in Uber's bonus program, and may
            be offered an equity award &amp; other types of comp. You will also
            be eligible for various benefits. More details can be found at the
            following link{" "}
            <a href="https://www.uber.com/careers/benefits">
              https://www.uber.com/careers/benefits
            </a>
            .
          </p>
          <p>
            At Uber, we reimagine the way the world moves for the better. The
            idea was born on a snowy night in Paris in 2008, and ever since
            then, our DNA of reimagination and reinvention carries on. We’ve
            grown into a global platform moving people and things in
            ever-expanding ways, taking on big problems to help drivers, riders,
            delivery partners, and eaters make movement happen at the push of a
            button for everyone, everywhere.
          </p>
          <p>
            We welcome people from all backgrounds who seek the opportunity to
            help build a future where everyone and everything can move
            independently. If you have the curiosity, passion, and collaborative
            spirit, work with us, and let’s move the world forward, together.
          </p>
          <p>
            Uber is proud to be an Equal Opportunity/Affirmative Action
            employer. All qualified applicants will receive consideration for
            employment without regard to sex, gender identity, sexual
            orientation, race, color, religion, national origin, disability,
            protected Veteran status, age, or any other characteristic protected
            by law. We also consider qualified applicants regardless of criminal
            histories, consistent with legal requirements. If you have a
            disability or special need that requires accommodation, please let
            us know by completing{" "}
            <a href="https://forms.gle/aDWTk9k6xtMU25Y5A">this form</a>.
          </p>
          <p>
            Offices continue to be central to collaboration and Uber’s cultural
            identity. Unless formally approved to work fully remotely, Uber
            expects employees to spend at least half of their work time in their
            assigned office. For certain roles, such as those based at
            green-light hubs, employees are expected to be in-office for 100% of
            their time. Please speak with your recruiter to better understand
            in-office expectations for this role.
          </p>
          <p>
            Uber is committed to a safe workplace. We have implemented COVID-19
            safety protocols that meet or exceed local public health guidelines.
            Workplace safety remains our number one priority. As a result, Uber
            recommends all US- and Canada-based employees to be vaccinated in
            order to access any of our facilities; this is subject to change
            solely at the Company’s discretion.
          </p>
        </div>
      </main>
      <SupportCard />
    </div>
  );
}
